<?php

namespace App\Models;

use App\Mail\SendOtpCode;
use AmazingTraits\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class m_users extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $table;
    /**
     * @var string[] // ['default', 'get', 'find', 'store', 'update', 'destroy', 'custom*', 'getfunc*']
     */
    public $auths = ['get', 'find', 'store', 'update', 'destroy'];
    public $description = "Adalah kumpulan master data user";

    protected $casts = [
        'active_flag' => 'boolean',
        'roles' => 'array'
    ];

    public function __construct($attr = array(), $exists = false)
    {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'm_users';
    }

    /**
     * Get the channels that model events should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(string $event): array
    {
        return [
            new PrivateChannel('user.' . $this->id)
        ];
    }

    /**
     * Untuk auto join saat get data
     */
    protected $joins = [
        'created_id' => [m_users::class, 'username,name'],
        'updated_id' => [m_users::class, 'username,name'],
        'deleted_id' => [m_users::class, 'username,name'],
        'province_id' => [m_general::class, 'value_1,value_3'],
        'regency_id' => [m_general::class, 'value_1,value_3'],
        'know_from_id' => [m_general::class, 'value_1'],
    ];

    /**
     * Untuk auto mengurusi detail saat Create/Update/Delete data, mirip hasMany tapi versi simple
     */
    public $details = [
        'm_users_d_device_histories',
        'm_users_d_login_histories'
    ];

    /**
     * Fungsi untuk validation saat create
     */
    public function creatingRules(): array
    {
        return [
            'avatar' => 'nullable|string',
            'username' => "required|string|unique:{$this->getOnlyTable()},username,NULL,id,deleted_at,NULL",
            'email' => "nullable|email|unique:{$this->getOnlyTable()},email,NULL,id,deleted_at,NULL",
            'phone' => "required|string|unique:{$this->getOnlyTable()},phone,NULL,id,deleted_at,NULL",
            'name' => 'required|string',
            'gender' => 'nullable|string',
            'date_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'province_id' => 'nullable|string',
            'regency_id' => 'nullable|string',
            'roles' => 'nullable|array',
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
            'active_flag' => 'nullable|boolean'
        ];
    }

    /**
     * Fungsi untuk validation saat update
     */
    public function updatingRules(int $id): array
    {
        $rules = $this->creatingRules(); // menyamakan dengan create, tapi diubah sedikit
        foreach ($rules as $key => $value) {
            if ($key == 'email') {
                $rules[$key] = str_replace("email,NULL", "email,$id", $value);
            } else if ($key == 'username') {
                $rules[$key] = str_replace("username,NULL", "username,$id", $value);
            } else if ($key == 'phone') {
                $rules[$key] = str_replace("phone,NULL", "phone,$id", $value);
            } else if ($key == 'password') {
                $rules[$key] = 'nullable|string|min:6';
                $rules['c_password'] = 'nullable|same:password';
            }
        }
        return $rules;
    }

    // update cp_m_roles_d_users & cp_m_users 'role' field
    public function updateRoles($model)
    {
        DB::beginTransaction();
        try {
            if (count($model->roles) === 1) {
                $model->role = $model->roles[0];
            }

            //hapus semua dahulu data user yang nempel di roles_d_users sebelum update data detail
            DB::table('cp_m_roles_d_users')
                ->where('user_id', $this->autoDecrypt($model->id))
                ->delete();

            foreach ($model->roles as $role) {
                $role = DB::table('cp_m_roles')
                    ->where('name', $role)
                    ->where('active_flag', 1)
                    ->where('deleted_at', '=', null)
                    ->first();
                if ($role) {
                    DB::table('cp_m_roles_d_users')
                        ->insert([
                            'cp_m_roles_id' => $role->id,
                            'user_id' => $this->autoDecrypt($model->id),
                            'created_id' => $this->getUserId(),
                            'updated_id' => $this->getUserId()
                        ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function onCreating($model): void
    {
        $model->password = Hash::make($model->password);
        $model->created_id = $this->getUserId();

        if (count($model->roles)) {
            $model->role = $model->roles[0];
        }
    }

    public function onCreated($model): void
    {
        $createdId = $this->autoDecrypt($this->getUserId());

        $roles = DB::table('cp_m_roles')
            ->whereIn('name', $model->roles)
            ->where('active_flag', 1)
            ->where('deleted_at', '=', null)
            ->get()
            ->toArray();

        $roles_d_users_data[] = [
            'user_id' => $this->autoDecrypt($model->id),
            'cp_m_roles_id' => $roles[0]->id,
            'created_at' => now(),
            'updated_at' => now(),
            'created_id' => $createdId
        ];

        DB::table('cp_m_roles_d_users')->insert($roles_d_users_data);
    }

    public function onSaved($model): void
    {
        $this->updateClusterDetail($model);
    }

    /**
     * Tambahan saat update (fitur trait)
     */
    public function onUpdating($model): void
    {
        DB::beginTransaction();
        try {
            if ($model->password) {
                $model->password = Hash::make($model->password);
            } else {
                $user = DB::table($this->getOnlyTable())->where('id', $this->autoDecrypt($model->id))->first();
                if ($user) {
                    $model->password = $user->password;
                }
            }

            $model->updated_id = $model->getUserId();
            if (isset($model->roles) && count($model->roles)) {
                $oldModel = m_users::where('id', $this->autoDecrypt($model->id))
                    ->where('deleted_at', '=', null)
                    ->first();

                // melakukan pengecekan apakah roles yang lama itu berbeda dengan roles yang baru
                if (array_diff($model->roles, $oldModel->roles)) {
                    // melakukan update role & roles
                    $this->updateRoles($model);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Tambahan saat read (fitur trait)
     */
    public function onRetrieved($model): void
    {
        $model->meta_update = true;
        $model->meta_delete = true;
        $model->password = null;
    }

    public function onDeleting($model): void
    {
        $model->deleted_id = $model->getUserId();
    }

    public function customChange_role_personaly(): \Illuminate\Http\JsonResponse
    {
        $req = app()->request;
        $role_target = $req->role_target;
        $table_prefix = env('TABLE_PREFIX');
        $petik = '"';
        $valFix = "'%{$petik}$role_target{$petik}%'";
        $user = DB::table($table_prefix . 'm_users')
            ->where('id', $this->getUserId())
            ->orWhereJsonContains("roles", $role_target);

        if ($user->exists()) {
            $user->update([
                'role' => $role_target,
                'updated_id' => $this->getUserId(),
                'updated_at' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => "Role has been change to $role_target",
                'data' => $this->getMe()
            ]);
        } else {
            return sendError('Role not found', [], 404);
        }
    }

    public function customChange_password()
    {
        $request = app()->request;

        $validatedData = $request->validate([
            'old_password' => ['required', new MatchOldPassword],
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
        ]);

        $validatedData = [
            'password' => Hash::make($validatedData['password']),
        ];

        DB::table('cp_m_users')->where('id', $this->getUserId())->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Successfully changed password'
        ]);
    }
}
