<?php

namespace App\Http\Controllers\API;

class MainDocsController
{
    public array $template = [];

    public function __construct()
    {
        $this->template = [
            [
                'description' => '',
                'model' => 'Auth',
                'items' => [
                    [
                        'type' => 'POST',
                        'path' => 'api/signin',
                        'validation' => json_encode([
                            'identity' => 'required|string',
                            'password' => 'required|string',
                            'device' => [
                                'uuid' => 'required|string|max:150',
                                'brand' => 'required|string|string|max:20',
                                'os' => 'required|string|max:20',
                                'version' => 'required|string|max:20',
                                'app_version' => 'nullable|string|max:10'
                            ],
                            'longlat_point' => '-7.223131313, 114.23123123'
                        ])
                    ],
                    [
                        'type' => 'POST',
                        'path' => 'api/signup-caleg',
                        'validation' => json_encode([
                            'name' => 'required|string',
                            'gender' => 'required|in:Pria,Wanita',
                            'birth_date' => 'required|date',
                            'nik' => 'required|string|max:16',
                            'phone' => 'required|string|unique:' . env('TABLE_PREFIX') . 'm_users,phone',
                            'email' => 'required|email|unique:' . env('TABLE_PREFIX') . 'm_users,email',
                            'agama' => 'required|string|max:25',
                            'partai_id' => 'required|integer',
                            'no_kta' => 'nullable|string|max:25',
                            'tipe_caleg_id' => 'required|integer',
                            // 'dapil_id' => 'required|integer',
                            'nama_dapil' => 'required|string',
                            'nomor_urut' => 'required|integer',
                            'prov_id' => 'required|integer',
                            'kab_id' => 'required|integer',
                            'kec_id' => 'required|integer',
                            'kel_id' => 'required|integer',
                            'koordinat_point' => 'required|string',
                        ]),
                        'screenshot' => [
                            'url' => '/img/ss/Screenshot 2023-11-09 065615.png',
                            'figma_url' => 'https://www.figma.com/proto/Uyc08lH2PaKw2sApFHHbAn/APP-CALEG-PAN?type=design&node-id=2053-7698&t=PI8Tp3TCfbRixGdS-0&scaling=scale-down&page-id=0%3A1&starting-point-node-id=2053%3A18529',
                        ]
                    ],
                    [
                        'type' => 'POST',
                        'path' => 'api/signup-relawan',
                        'validation' => json_encode([
                            'name' => 'required|string|max:50',
                            'nik' => 'required|string|max:16',
                            'no_kk' => 'required|string|max:25',
                            'birth_date' => 'required|date',
                            'no_kta' => "string|max:25",
                            'phone' => 'required|string|max:15',
                            'email' => 'required|email|unique:cp_m_users,email',
                            'gender' => 'required|string|max:6',
                            'partai_id' => 'required|integer',
                            'relasi' => 'required|string|max:25',
                            'agama' => 'required|string|max:25',
                            'prov_id' => 'required',
                            'kab_id' => 'required',
                            'kec_id' => 'required',
                            'kel_id' => 'required',
                            'koordinat_point' => 'required',
                            'file1' => 'required|string|max:250',
                            'file2' => 'required|string|max:250',
                            'file3' => 'nullable|string|max:250',
                        ]),
                        'screenshot' => [
                            'url' => '/img/ss/Screenshot 2023-11-09 065935.png',
                            'figma_url' => 'https://www.figma.com/proto/Uyc08lH2PaKw2sApFHHbAn/APP-CALEG-PAN?type=design&node-id=2053-6382&t=PI8Tp3TCfbRixGdS-0&scaling=scale-down&page-id=0%3A1&starting-point-node-id=2053%3A18529',
                        ]
                    ],
                    [
                        'labels' => [
                            [
                                'text' => 'OTP Code Verification',
                                'bg-color' => 'bg-red-400',
                            ],
                            [
                                'text' => 'For Login Mobile',
                                'bg-color' => 'bg-orange-400',
                            ],
                            [
                                'text' => 'For Register Mobile',
                                'bg-color' => 'bg-orange-400',
                            ],
                        ],
                        'type' => 'GET',
                        'path' => 'api/otp-verification/:otp_code'
                    ],
                    [
                        'type' => 'GET',
                        'path' => 'api/me'
                    ],
                    [
                        'type' => 'POST',
                        'path' => 'api/signout',
                        'validation' => ''
                    ]
                ]
            ],
            [
                'description' => '',
                'model' => 'File',
                'items' => [
                    [
                        'type' => 'POST',
                        'path' => 'api/upload',
                        'validation' => json_encode([
                            'file' => 'required|binary'
                        ])
                    ],
                ]
            ]
        ];
    }
}
