import ObjectReader from '~/services/ObjectReader'
// import JobsWorker from '~/services/JobsWorker'
import ObjectUpdater from '~/services/ObjectUpdater'
import IntToRupiah from '~/services/IntToRupiah'
import RupiahToInt from '~/services/RupiahToInt'
import Preparation from '~/services/Preparation/index'
import GetRandomString from '~/services/GetRandomString'
import ClearArray from '~/services/ClearArray'
import OnAddNewRow from '~/services/OnAddNewRow'
import RupiahFixValue from '~/services/RupiahFixValue'
import CheckString from '~/services/CheckString'
import Transform from '~/services/Transform'
import CheckPermissions from '~/services/CheckPermissions'
import DownloadFile from '~/services/DownloadFile'
import UppercaseFirst from '~/services/UppercaseFirst'
import Time from '~/services/Time'
import SortArray from '~/services/SortArray'
import Swal from '~/services/Swal'
import Notyf from '~/services/Notyf'
import Flatpickr from '~/services/Flatpickr'
import PhoneFormatter from '~/services/PhoneFormatter'
import Auth from '~/services/Auth'
import Axios from '~/services/Axios'
import UpdateWhereParams from '~/services/UpdateWhereParams'
import SendError from '~/services/SendError'
import SyncronizeMenu from '~/services/SyncronizeMenu'
import GenerateRandomHexColor from '~/services/GenerateRandomHexColor'
import TextTruncate from '~/services/TextTruncate'
import t from './t'
import DetailFixValue from '~/services/DetailFixValue'
import Cryptor from '~/services/Cryptor'

export {
  Cryptor,
  DetailFixValue,
  t,
  TextTruncate,
  GenerateRandomHexColor,
  SyncronizeMenu,
  SendError,
  UpdateWhereParams,
  Axios,
  Auth,
  PhoneFormatter,
  Flatpickr,
  Notyf,
  Swal,
  SortArray,
  Time,
  UppercaseFirst,
  DownloadFile,
  CheckPermissions,
  Transform,
  CheckString,
  RupiahFixValue,
  OnAddNewRow,
  ClearArray,
  GetRandomString,
  ObjectUpdater,
  ObjectReader,
  // JobsWorker,
  IntToRupiah,
  RupiahToInt,
  Preparation,
}
