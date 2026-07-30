<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\User\AgencyMainController;
use App\Http\Controllers\User\UserMainController;
use App\Http\Controllers\User\RepairController;
use App\Http\Controllers\User\VehiclesController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\Admin\ManageCompanyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\User\ManageAccountController;
use App\Http\Controllers\User\UserProfileController;
use Database\Seeders\VehicleTypeSeeder;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\User\ManagerController;
use App\Http\Controllers\User\InspectionController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\StaffFormController;
use App\Http\Controllers\SupplyMainController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Agency\AgentCompanyController;
use App\Http\Controllers\CompanySupplyController;
use App\Http\Controllers\CompanyVehicleController;
use App\Http\Controllers\CompanyDashboardController;
use App\Http\Controllers\CompanyFormController;
use App\Http\Controllers\CompanyPreInspectionController;
use App\Http\Controllers\CompanyReportTemplateController;
use App\Http\Controllers\Staff\StaffReportTemplateController;
use App\Http\Controllers\Staff\StaffPreInspectionController;
use App\Http\Controllers\Staff\FormGroupController;
use App\Http\Controllers\Staff\InspectorController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\Staff\VehicleController;
use App\Http\Controllers\Inspector\InspectorProfileController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Staff\StaffSupplyController;
use App\Http\Controllers\Staff\InsuranceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/', [RedirectController::class, 'handleRoot'])->name('root');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/comingsoon', [PageController::class, 'coming_soon'])->name('coming_soon');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    //CRUD บริษัทฯว่าจ้าง
    Route::get('/cp_list', [AdminDashboardController::class, 'CompanyList'])->name('admin.cp_list');
    Route::get('/cp_new', [AdminDashboardController::class, 'CompanyCreate'])->name('admin.cp_create');
    Route::get('/cp_edit/{id}', [AdminDashboardController::class, 'CompanyEdit'])->name('admin.cp_edit');

    Route::POST('/cp_insert', [ManageCompanyController::class, 'CompanyStore'])->name('admin.cp_store');

    Route::POST('/cp_update/{id}/{tab}', [ManageCompanyController::class, 'CompanyUpdate'])->name('admin.cp_update');

    Route::get('/cp_status/{id}/{status}', [ManageCompanyController::class, 'UpdateStatus'])->name('admin.cp_updatestatus');

    //CRUD Supply
    Route::get('/supply-all', [ManageCompanyController::class, 'SupplyAll'])->name('admin.supply_all');

    Route::get('/sup_edit/{id}', [ManageCompanyController::class, 'SupplyEdit'])->name('admin.sup_edit');

    Route::POST('/sup_update/{id}/{tab}', [ManageCompanyController::class, 'SupplyUpdate'])->name('admin.sup_update');

    Route::get('/sup_list/{id}', [ManageCompanyController::class, 'SupList'])->name('admin.sup_list');
    Route::get('/sup-create/{id}', [ManageCompanyController::class, 'SupCreate'])->name('admin.sup_create');
    Route::post('/supply/insert', [ManageCompanyController::class, 'SupInsert'])->name('admin.sup_insert');

    // CRUD หน่วยงาน
    Route::get('/agencies/create', [ManageUserController::class, 'createAgency'])->name('admin.agency.create');
    Route::post('/agencies/insert', [ManageUserController::class, 'insert_agency'])->name('admin.agency.insert');
    Route::get('/agency', [ManageUserController::class, 'Agency_list'])->name('admin.agency_list');

    Route::get('/agencies/{id}/edit', [ManageUserController::class, 'EditAgency'])->name('admin.agency.edit');
    Route::put('/agencies/{id}', [ManageUserController::class, 'UpdateAgency'])->name('admin.agency.update');
    Route::GET('/agencies/{id}', [ManageUserController::class, 'DestroyAgency'])->name('admin.agency.destroy');
    Route::get('/show-agencies/{id}', [ManageUserController::class, 'AgencyDetail'])->name('admin.agency.show');

    //CRUD User-Manager
    Route::get('/members/create/{role}/{id}', [ManageUserController::class, 'createMember'])->name('admin.member.create');
    Route::post('/members/insert', [ManageUserController::class, 'insertMember'])->name('admin.member.insert');
    Route::get('/members/{id}/edit', [ManageUserController::class, 'editMember'])->name('admin.member.edit');
    Route::put('/members/{id}', [ManageUserController::class, 'updateMember'])->name('admin.member.update');
    Route::delete('/members/{id}', [ManageUserController::class, 'destroyMember'])->name('admin.member.destroy');

    //module ประกาศ
    Route::get('/announcement', [AdminDashboardController::class, 'AnnouncementPage'])->name('admin.announce');
    Route::get('/create_post', [AdminDashboardController::class, 'create_announce'])->name('admin.create_post');
    Route::post('/insert_post', [AdminDashboardController::class, 'insert_post'])->name('admin.insert_post');

    //edit-update-delete_post
    Route::get('/announce/{id}/edit', [AdminDashboardController::class, 'edit_post'])->name('admin.edit_post');
    Route::post('/announce-update/{id}', [AdminDashboardController::class, 'update_post'])->name('admin.update_post');
    Route::get('/announce-delete/{id}/file', [AdminDashboardController::class, 'delete_file'])->name('admin.delete_file');
    Route::get('/announce-delete/{id}/post', [AdminDashboardController::class, 'delete_post'])->name('admin.delete_post');
});

Route::prefix('vehicles')->middleware(['auth', 'role:user,supply,staff,manager,admin,agency,inspector,company'])->group(function () {
    Route::get('/page/{id}', [VehiclesController::class, 'veh_detail'])->name('veh.detail');
    Route::get('/result/{rec}', [VehiclesController::class, 'Report_Result'])->name('veh.result');
    Route::get('/repair-notice', [VehiclesController::class, 'repair_notice'])->name('veh.notice');

    Route::get('/form-report/{rec}', [VehiclesController::class, 'Form_report'])->name('form_report');
    Route::get('/form-image/{rec}', [VehiclesController::class, 'Form_Image8'])->name('form_image8');
    Route::get('/form-image-fail/{rec}', [VehiclesController::class, 'FormImage_Fail'])->name('form_imagefail');

    //------vehicle------//
    // Vehicle CRUD
    Route::name('vehicles.')->group(function () {

        // AJAX endpoints
        Route::get('/ajax/supplies-by-company', [VehicleController::class, 'getSuppliesByCompany'])
            ->name('ajax.supplies');
        Route::get('/ajax/check-plate', [VehicleController::class, 'checkPlateUnique'])
            ->name('ajax.check_plate');
        Route::get('/ajax/supply-info/{sup_id}', [VehicleController::class, 'getSupplyInfo'])
            ->name('ajax.supply_info');
        Route::post('/ajax/change-status/{veh_id}', [VehicleController::class, 'changeStatus'])
            ->name('ajax.change_status');

        // List & Create
        Route::get('/', [VehicleController::class, 'index'])->name('index');
        Route::get('/create', [VehicleController::class, 'create'])->name('create');
        Route::post('/', [VehicleController::class, 'store'])->name('store');

        Route::get('/ajax',[VehicleController::class, 'ajaxIndex']) ->name('ajax.index');

        // Show / Edit / Update / Delete
        Route::get('/{veh_id}', [VehicleController::class, 'show'])->name('show');
        Route::get('/{veh_id}/edit', [VehicleController::class, 'edit'])->name('edit');
        Route::put('/{veh_id}', [VehicleController::class, 'update'])->name('update');
        Route::delete('/{veh_id}', [VehicleController::class, 'destroy'])->name('destroy');


        // Document management
        Route::prefix('{veh_id}/documents')->name('documents.')->group(function () {
            Route::get('/', [VehicleController::class, 'documentList'])->name('list');
            Route::post('/upload', [VehicleController::class, 'documentUpload'])->name('upload');
            Route::get('/{doc_id}/download', [VehicleController::class, 'documentDownload'])->name('download');
            Route::delete('/{doc_id}', [VehicleController::class, 'documentDelete'])->name('delete');
        });
             
    });
});

Route::middleware(['auth', 'role:company,supply,staff'])->group(function () {
    Route::post('/drivers/check-duplicate', [DriverController::class, 'checkDuplicate'])
         ->name('drivers.checkDuplicate');
    Route::get('/get-supplies-by-company', [DriverController::class, 'getSupplies'])
         ->name('drivers.getSupplies');

    Route::resource('drivers', DriverController::class);


    Route::post('/drivers/{id}/documents', [DriverController::class, 'documentStore'])
         ->name('drivers.documents.store');
    Route::delete('/drivers/{id}/documents/{docId}', [DriverController::class, 'documentDestroy'])
         ->name('drivers.documents.destroy');
});

Route::prefix('inspection')->middleware(['auth', 'role:user,inspector,company,staff'])->name('inspection.')->group(function () {
    Route::get('/', [InspectionController::class, 'index'])->name('index');
    Route::get('/search-vehicle', [InspectionController::class, 'searchVehicle'])->name('searchVehicle');
    Route::post('/start', [InspectionController::class, 'start'])->name('start');
    Route::get('/{record_id}/step2', [InspectionController::class, 'step2'])->name('step2');
    Route::post('/{record_id}/step2', [InspectionController::class, 'storeStep2'])->name('storeStep2');
    // Step 3: หน้าจอข้อตรวจหลัก
    Route::get('/{record_id}/step3', [InspectionController::class, 'step3'])->name('step3');
    Route::post('/save-result', [InspectionController::class, 'saveResult'])->name('saveResult');
    // API สำหรับอัปโหลดและลบรูปภาพ (เฉพาะข้อ)
    Route::post('/upload-item-image', [InspectionController::class, 'uploadItemImage'])->name('uploadItemImage');
    Route::post('/delete-item-image', [InspectionController::class, 'deleteItemImage'])->name('deleteItemImage');

    Route::get('/{record_id}/step4', [InspectionController::class, 'step4'])->name('step4');
    Route::post('/{record_id}/submit', [InspectionController::class, 'submitInspection'])->name('submitInspection');
    Route::get('/{record_id}/viewreport', [InspectionController::class, 'viewReport'])->name('report');
    Route::get('/{record_id}/retest', [InspectionController::class, 'retest'])->name('retest');
});


Route::prefix('inspector')->middleware(['auth', 'role:inspector'])->group(function () {
    Route::get('/index', [PageController::class, 'home'])->name('ins-dashboard');
    Route::get('/profile', [InspectorProfileController::class, 'show'])->name('ins.profile');
    Route::post('/profile/update', [InspectorProfileController::class, 'update'])->name('ins.profile.update');
});

Route::prefix('user')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/home', [PageController::class, 'home'])->name('local.home');
    Route::get('/announce', [UserMainController::class, 'announce'])->name('user.announce');
    Route::get('/check/all', [UserMainController::class, 'chk_list'])->name('user.chk_list');
    Route::get('/profile', [UserMainController::class, 'profile'])->name('user.profile');

    //ลงทะเบียนรถ
    Route::get('/veh-regis', [UserMainController::class, 'veh_regis'])->name('user.veh_regis');
    Route::POST('/veh-create', [UserMainController::class, 'veh_insert'])->name('user.veh_create');
    //แก้ไขข้อมูลรถ
    Route::get('/veh-edit/{id}', [UserMainController::class, 'veh_edit'])->name('user.veh_edit');
    Route::POST('/veh-update', [UserMainController::class, 'veh_update'])->name('user.veh_update');
    //ระเบียนการตรวจ
    Route::get('/veh/{id}', [UserMainController::class, 'veh_detail'])->name('user.veh_detail');

    //เริ่มตรวจ
    Route::get('/check/start/{id}', [UserMainController::class, 'start_check'])->name('user.chk_start');
    Route::POST('/chk-insert/new1/{id}', [UserMainController::class, 'insert_new1'])->name('user.insert_new1');
    //Route::POST('/chk/insert/step1/{id}', [UserMainController::class, 'insert_step1'])->name('user.insert1');

    Route::get('/check/step2/{rec}/{cats}', [UserMainController::class, 'chk_step2'])->name('user.chk_step2');
    //Route::POST('/check-store/step2/{record_id}/{category_id}', [UserMainController::class, 'chk_insert_step2'])->name('user.chk_insert_step2');

    Route::get('/check/result/{record_id}', [UserMainController::class, 'chk_result'])->name('user.chk_result');

    //ปรับฟอร์มการตรวจแบบไม่เรียงหมวดหมู่ สามารถสลับหมวดหมู่การตรวจได้
    Route::get('/check/{record}/summary', [UserMainController::class, 'summary'])
        ->name('user.chk_summary');
    Route::POST('/check-store/{record_id}/{category_id}', [UserMainController::class, 'storeOrUpdate'])->name('user.storeOrUpdate');

    Route::post('/check/{record}/confirm', [UserMainController::class, 'confirm'])
        ->name('user.chk_confirm');

    //แก้ไขภาพ
    Route::get('/images/edit/{record_id}/{id}', [VehiclesController::class, 'edit_images'])->name('user.edit_images');
    Route::post('/images/update', [VehiclesController::class, 'update_image'])->name('update_image');
    Route::get('/image/delete/{id}', [VehiclesController::class, 'delete_image'])->name('delete_image');

    //แจ้งซ่อม
    Route::get('/create-repair/{record_id}', [RepairController::class, 'create_repair'])->name('user.create_repair');

    //บันทึกข้อความ
    Route::get('/veh-doc', [DocumentController::class, 'doc_list'])->name('user.doc_list');
});

Route::prefix('agency')->middleware(['auth', 'role:agency'])->group(function () {
    Route::get('/index', [PageController::class, 'home'])->name('agency.index');
    Route::get('/main', [AgencyMainController::class, 'main_page'])->name('agency.main');

    //เพิ่มรถ
    Route::get('/veh-regis', [VehiclesController::class, 'veh_regis'])->name('agency.veh_regis');
    Route::get('/veh-list/{id}', [VehiclesController::class, 'veh_list'])->name('agency.veh_list');
    Route::POST('/veh-create', [VehiclesController::class, 'veh_insert'])->name('agency.veh_create');

    //สร้างuserหัวหน้า-เจ้าหน้าที่
    Route::get('/managerlist', [ManageAccountController::class, 'ManagerList'])->name('agency.manager_list');
    Route::get('/userlist', [ManageAccountController::class, 'UserList'])->name('agency.user_list');
    Route::post('/check-username', [ManageAccountController::class, 'checkUsername'])->name('check.username');

    Route::get('/create-account/{role}', [ManageAccountController::class, 'createAccount'])->name('agency.create_account');
    Route::post('/insert_account', [ManageAccountController::class, 'InsertAccount'])->name('agency.insert_account');

    //ฟอร์ม
    Route::get('/form', [AgencyMainController::class, 'form_list'])->name('agency.form_list');
    Route::get('/create-form', [AgencyMainController::class, 'form_create'])->name('agency.create_form');
    Route::post('/insert_form', [AgencyMainController::class, 'form_insert'])->name('agency.insert_form');

    //หมวดหมู่
    Route::get('/chk-categories/{form_id}', [AgencyMainController::class, 'cates_list'])->name('agency.cates_list');
    Route::get('/chk-cates-create/{id}', [AgencyMainController::class, 'create_cates'])->name('agency.create_cates');
    Route::post('/insert_cates/{id}', [AgencyMainController::class, 'insert_cates'])->name('agency.insert_cates');
    Route::get('/categories/{cates_id}', [AgencyMainController::class, 'cates_detail'])->name('agency.cates_detail');

    //ข้อตรวจ
    Route::get('/item-new/{id}', [AgencyMainController::class, 'item_create'])->name('agency.item_create');
    Route::post('/insert-item', [AgencyMainController::class, 'item_insert'])->name('agency.item_insert');
    Route::get('/item-edit/{id}', [AgencyMainController::class, 'item_edit'])->name('agency.item_edit');
    Route::post('/item-update', [AgencyMainController::class, 'item_update'])->name('agency.item_update');
    Route::get('/item-delete/{id}/image', [AgencyMainController::class, 'item_delete_image'])->name('agency.item_delete_image');

    //CRUD บริษัทว่าจ้าง
    Route::get('/companies', [AgentCompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [AgentCompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [AgentCompanyController::class, 'store'])->name('companies.store');

    Route::get('/companies/{id}/edit', [AgentCompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{id}', [AgentCompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{id}/delete', [AgentCompanyController::class, 'destroy'])->name('companies.destroy');
});

Route::prefix('manager')->middleware(['auth', 'role:manager'])->group(function () {
    // สำหรับผจก.BU
    Route::get('/index', [PageController::class, 'home'])->name('manager.index');

    //ทะเบียนบริษัท
    Route::get('/company-regis', [ManagerController::class, 'company_register'])->name('manager.company_regis');
    Route::get('/company-list/{id}', [ManagerController::class, 'company_list'])->name('manager.company_list');
    Route::POST('/company-insert', [ManagerController::class, 'company_insert'])->name('manager.company_insert');
});

Route::prefix('company')->middleware(['auth', 'role:company'])->group(function () {
    // สำหรับบริษัทว่าจ้างฯ
    Route::get('/index', [PageController::class, 'home'])->name('company.index');

    // Profile
    Route::get('/profile', [CompanyProfileController::class, 'show'])->name('company.profile');
    Route::put('/profile', [CompanyProfileController::class, 'update'])->name('company.profile.update');

    // CRUD สำหรับ Supply
    Route::get('/supplies/show/{id}', [CompanySupplyController::class, 'SupShow'])->name('company.supplies.show');
    Route::get('/supplies', [CompanySupplyController::class, 'SupIndex'])->name('company.supplies.index');
    Route::get('/supplies/create', [CompanySupplyController::class, 'create'])->name('company.supplies.create');
    Route::post('/supplies', [CompanySupplyController::class, 'store'])->name('company.supplies.store');
    Route::get('/supplies/{id}/edit', [CompanySupplyController::class, 'edit'])->name('company.supplies.edit');
    Route::put('/supplies/{id}', [CompanySupplyController::class, 'update'])->name('company.supplies.update');
    Route::delete('/company/supplies/{id}', [CompanySupplyController::class, 'destroy'])->name('company.supplies.destroy');

    //CRUD จัดการรถ Supply
    Route::get('/company/supplies/{supply_id}/vehicles/create', [CompanyVehicleController::class, 'create'])->name('company.vehicles.create');
    Route::post('/company/supplies/{supply_id}/vehicles', [CompanyVehicleController::class, 'store'])->name('company.vehicles.store');

    Route::get('/company/vehicles/{id}/edit', [CompanyVehicleController::class, 'edit'])->name('company.vehicles.edit');
    Route::put('/company/vehicles/{id}/update', [CompanyVehicleController::class, 'car_update'])->name('company.vehicles.update');

    Route::delete('/company/vehicles/{id}', [CompanyVehicleController::class, 'car_destroy'])->name('company.vehicles.destroy');

     Route::get('/vehicles/list', [CompanyVehicleController::class, 'VehiclesList'])->name('company.vehicles.list');

    Route::get('/inform', [CompanyDashboardController::class, 'vehicles_information'])->name('company.vehicles.inform');
    Route::get('/inform/fail-items/{record_id}', [CompanyDashboardController::class, 'failedItems'])->name('company.vehicles.fail_items');

    //CRUD ฟอร์มเช็ค
    Route::get('/form', [CompanyDashboardController::class, 'company_form'])->name('company.form.index');
    Route::get('/form/create', [CompanyFormController::class, 'create'])->name('company.form.create');
    Route::POST('/form/store', [CompanyFormController::class, 'FormStore1'])->name('company.form_store1');

    Route::get('/form/create/step2/{id}/{order}', [CompanyFormController::class, 'FormNew_Step2'])->name('company.form.create2');

    Route::POST('/form/store/step2/{id}', [CompanyFormController::class, 'FormStep2_Store'])->name('company.form.store2');

    Route::get('/form/create/step3/{id}', [CompanyFormController::class, 'FormNew_Step3'])->name('company.form.create3');

    //back แบบเคลียร์ session
    Route::get('/form/back-to-create3/{id}', [CompanyFormController::class, 'backToCreate3'])
        ->name('company.form.back_to_create3');

    Route::get('/form/categories/{cates_id}', [CompanyFormController::class, 'categories_list'])->name('company.form.categories_list');

    Route::get('/form/item-create/{id}', [CompanyFormController::class, 'item_create'])->name('company.form.ItemCreate');
    Route::post('/form/item-store', [CompanyFormController::class, 'item_store'])->name('company.form.ItemStore');

    //CRUD Template Report
    Route::prefix('company/pre-inspection')->name('company.pre_inspection.')->group(function () {
        Route::get('/', [CompanyPreInspectionController::class, 'index'])->name('index');
        Route::get('/create', [CompanyPreInspectionController::class, 'create'])->name('create');
        Route::post('/store', [CompanyPreInspectionController::class, 'pre_ins_store'])->name('store');

        Route::get('/{id}', [CompanyPreInspectionController::class, 'show'])->name('show');
    });

    Route::prefix('company/report-template')->name('company.report_template.')->group(function () {
        Route::get('/', [CompanyReportTemplateController::class, 'index'])->name('index');
        Route::get('/create', [CompanyReportTemplateController::class, 'create'])->name('create');
        Route::post('/store', [CompanyReportTemplateController::class, 'store'])->name('store');
        Route::get('/show/{id}', [CompanyReportTemplateController::class, 'show'])->name('show');
    });

    Route::prefix('company/form-groups')->name('company.form-groups.')->group(function () {
        Route::get('/', [CompanyReportTemplateController::class, 'indexFormGroup'])->name('index');
        Route::get('/create', [CompanyReportTemplateController::class, 'createFormGroup'])->name('create');
        Route::post('/store', [CompanyReportTemplateController::class, 'storeFormGroup'])->name('store');
        Route::get('/{id}', [CompanyReportTemplateController::class, 'showFormGroup'])->name('show');
    });
});

Route::prefix('supply')->middleware(['auth', 'role:supply'])->group(function () {
    // Route สำหรับ supply
    Route::get('/home', [PageController::class, 'home'])->name('supply.home');

    //CRUD_Vehicles
    Route::get('/create_veh', [SupplyMainController::class, 'Vehicles_Create'])->name('supply.new_veh');
    Route::POST('/store-veh', [SupplyMainController::class, 'VehiclesInsert'])->name('supply.veh_store');
    Route::get('/list-veh', [SupplyMainController::class, 'VehiclesList'])->name('supply.veh_list');

    //CRUD_ช่างตรวจ
    Route::get('/inspector/{id}', [SupplyMainController::class, 'InspectorList'])->name('supply.inspector_list');
    Route::get('/inspector-new', [SupplyMainController::class, 'Inspector_Create'])->name('supply.inspector_new');
    Route::POST('/ins-store', [SupplyMainController::class, 'Inspector_Store'])->name('supply.inspector_store');

    //รายการตรวจ
    Route::get('/check/all', [SupplyMainController::class, 'chk_list'])->name('supply.chk_list');
});

Route::prefix('staff')->middleware(['auth', 'role:staff,company'])->group(function () {
    // Route สำหรับ staff
    Route::get('/index', [PageController::class, 'home'])->name('staff.index');

    //ลงทะเบียนรถ
    Route::get('/veh-list', [StaffController::class, 'VehiclesList'])->name('staff.veh_list');
    Route::get('/veh-regis', [StaffController::class, 'VehiclesRegister'])->name('staff.veh_regis');
    Route::POST('/veh-create', [StaffController::class, 'VehiclesInsert'])->name('staff.veh_create');
    Route::get('/get-supply', [StaffController::class, 'getSupplyByCompany'])->name('get.supply');

    //บริษัทแม่
    Route::get('/cp-list', [StaffController::class, 'CompanyList'])->name('staff.comp_list');
    Route::get('/supply-list', [StaffController::class, 'SupList'])->name('staff.supply_list');
    Route::get('/cp_supply/{id}', [StaffController::class, 'Company_SupList'])->name('staff.cp_sup');

    //ช่างตรวจ
    //Route::get('/inspector', [StaffController::class, 'InspectorList'])->name('staff.inspector_list');
    //Route::get('/inspector-new', [StaffController::class, 'Inspector_Create'])->name('staff.inspector_new');
    //Route::POST('/ins-store', [StaffController::class, 'Inspector_Store'])->name('staff.ins_store');


    Route::name('staff.')->group(function () {
        Route::resource('inspectors', InspectorController::class);
    });

    Route::prefix('report-template')->name('staff.report_template.')->group(function () {
        Route::get('/', [StaffReportTemplateController::class, 'index'])->name('index');
        Route::get('/create', [StaffReportTemplateController::class, 'create'])->name('create');
        Route::post('/store', [StaffReportTemplateController::class, 'store'])->name('store');
        Route::get('/show/{id}', [StaffReportTemplateController::class, 'show'])->name('show');
        Route::get('/report-preview/{id}', [StaffReportTemplateController::class, 'report_preview'])->name('report_preview');
    });

    Route::prefix('pre-inspection')->name('staff.pre_inspection.')->group(function () {
        Route::get('/', [StaffPreInspectionController::class, 'index'])->name('index');
        Route::get('/create', [StaffPreInspectionController::class, 'create'])->name('create');
        Route::post('/store', [StaffPreInspectionController::class, 'pre_ins_store'])->name('store');

        Route::get('/{id}', [StaffPreInspectionController::class, 'show'])->name('show');
    });

    Route::prefix('form-group')->name('staff.form-group.')->group(function () {
        Route::get('/', [FormGroupController::class, 'index'])->name('index');
        Route::get('/create', [FormGroupController::class, 'createFormGroup'])->name('create');
        Route::post('/store', [FormGroupController::class, 'store'])->name('store');
        Route::get('/{id}/show', [FormGroupController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FormGroupController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [FormGroupController::class, 'update'])->name('update');
    });

    // Supply CRUD (staff only)
    Route::prefix('supplies')->name('staff.supplies.')->group(function () {
        Route::get('/',          [StaffSupplyController::class, 'index'])  ->name('index');
        Route::get('/create',    [StaffSupplyController::class, 'create']) ->name('create');
        Route::post('/',         [StaffSupplyController::class, 'store'])  ->name('store');
        Route::get('/{id}',      [StaffSupplyController::class, 'show'])   ->name('show');
        Route::get('/{id}/edit', [StaffSupplyController::class, 'edit'])   ->name('edit');
        Route::put('/{id}',      [StaffSupplyController::class, 'update']) ->name('update');
        Route::delete('/{id}',   [StaffSupplyController::class, 'destroy'])->name('destroy');
    });

    // Insurance companies CRUD (staff only)
    Route::prefix('insurance')->name('staff.insurance.')->group(function () {
        Route::get('/',          [InsuranceController::class, 'index'])  ->name('index');
        Route::get('/create',    [InsuranceController::class, 'create']) ->name('create');
        Route::post('/',         [InsuranceController::class, 'store'])  ->name('store');
        Route::get('/{id}/edit', [InsuranceController::class, 'edit'])   ->name('edit');
        Route::put('/{id}',      [InsuranceController::class, 'update']) ->name('update');
        Route::delete('/{id}',   [InsuranceController::class, 'destroy'])->name('destroy');
    });

});

Route::prefix('form')->middleware(['auth', 'role:staff'])->group(function () {
    //จัดการฟอร์มตรวจของ staff
    Route::get('/list', [StaffFormController::class, 'FormList'])->name('staff.form_list');
    Route::get('/new-form', [StaffFormController::class, 'FormNew'])->name('staff.form_new');
    Route::POST('/save-form', [StaffFormController::class, 'FormStore'])->name('staff.form_store');

    Route::get('/preview-form/{form_id}', [StaffFormController::class, 'PreviewForm'])->name('staff.preview_form');

    Route::get('/new-form-2/{id}/{order}', [StaffFormController::class, 'FormNew_Step2'])->name('staff.form_new2');
    Route::POST('/save-form-step2/{id}', [StaffFormController::class, 'FormStep2_Insert'])->name('staff.form_store2');

    Route::get('/categories/{id}', [StaffFormController::class, 'FormNew_Step3'])->name('staff.form_step3');
    Route::get('/cate-detail/{cates_id}', [StaffFormController::class, 'categories_detail'])->name('staff.categories_detail');
    //แก้ไขชื่อหมวดหมู่
    Route::post('/categories/{id}/update', [StaffFormController::class, 'cates_update'])->name('staff.cates_update');
    //ลบหมวดหมู่
    Route::post('/categories/{id}/delete', [StaffFormController::class, 'cates_delete'])->name('staff.cates_delete');

    Route::get('/item-create/{id}', [StaffFormController::class, 'item_create'])->name('staff.ItemCreate');
    Route::post('/insert-item', [StaffFormController::class, 'item_insert'])->name('staff.item_insert');

    Route::get('/item-edit/{id}', [StaffFormController::class, 'item_edit'])->name('staff.item_edit');
    Route::post('/item-update', [StaffFormController::class, 'item_update'])->name('staff.item_update');
    Route::get('/item-delete/{id}/image', [StaffFormController::class, 'item_delete_image'])->name('staff.item_delete_image');

    //เพิ่มข้อตรวจต่อจากเดิม
    Route::get('/item-plus/{id}', [StaffFormController::class, 'item_create_plus'])->name('staff.item_create_plus');

    //group_form

});

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/read/{id}', [NotificationController::class, 'read'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
        ->name('notifications.readAll');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    Route::get('/register', [LoginController::class, 'showregisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register_store'])->name('register.store');
    Route::post('/check_username', [LoginController::class, 'check_username'])->name('username.check');
});


Route::get('/check-username', function () {
    $username = request('company_user');
    $exists = \App\Models\User::where('username', $username)->exists();
    return response()->json(['exists' => $exists]);
});





//Route::post('/logout',[AuthController::class,'logout'])->name('logout')->middleware('auth');
//Route::get('/lang/{lang}',[ LanguageController::class,'switchLang'])->name('switch_lang');
//Route::get('/pagination-per-page/{per_page}',[ PaginationController::class,'set_pagination_per_page'])->name('pagination_per_page');
