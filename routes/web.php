<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Dashboard\ArticleController;
use App\Http\Controllers\Dashboard\ElderlyController;
use App\Http\Controllers\Dashboard\OfficerController;
use App\Http\Controllers\Dashboard\VaccineController;
use App\Http\Controllers\Dashboard\MedicineController;
use App\Http\Controllers\Dashboard\WeighingController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ElderlyCheckController;
use App\Http\Controllers\Dashboard\FamilyParentController;
use App\Http\Controllers\Dashboard\ImmunizationController;
use App\Http\Controllers\Dashboard\SiteIdentityController;
use App\Http\Controllers\Dashboard\EventScheduleController;
// use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Dashboard\FamilyChildrenController;
use App\Http\Controllers\Dashboard\PregnancyCheckController;

// Landing Page
Route::get('/landingpage', [LandingPageController::class, 'index'])->name('home');
Route::get('/article/{slug}', [LandingPageController::class, 'showArticle'])->name('article.show');

// Login
Route::controller(AuthController::class)->middleware('guest')->group(function () {
    Route::get('/', 'index')->name('login');
    Route::post('/auth-login', 'authenticateLogin')->name('auth-login');
});
// Register
Route::controller(AuthController::class)->middleware('guest')->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/auth-register', 'authenticateRegister')->name('auth-register');
});
// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Ambil data dari API untuk Lokasi
Route::get('/get-provinces', [LocationController::class, 'getProvinces']);
Route::get('/get-cities/{province_id}', [LocationController::class, 'getCities']);
Route::get('/get-districts/{city_id}', [LocationController::class, 'getDistricts']);
Route::get('/get-villages/{district_id}', [LocationController::class, 'getVillages']);

// API Routes
Route::get('/api/anthropometric-standards', [App\Http\Controllers\Api\AnthropometricStandardController::class, 'index']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('/clear-dashboard-cache', [DashboardController::class, 'clearAllDashboardCache'])->name('clear-dashboard-cache')->middleware('auth');

// Pastikan ada route seperti ini:
// Route::get('/children-nutrition-status/ajax/{year}', [DashboardController::class, 'childrenNutritionStatusAjax']);

// Articles Management
Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/article', [ArticleController::class, 'index'])->name('article.index');
    Route::get('/article/create', [ArticleController::class, 'create'])->name('article.create');
    Route::post('/article', [ArticleController::class, 'store'])->name('article.store');
    Route::get('/article/{article}/edit', [ArticleController::class, 'edit'])->name('article.edit');
    Route::put('/article/{article}', [ArticleController::class, 'update'])->name('article.update');
    Route::delete('/article/{article}', [ArticleController::class, 'destroy'])->name('article.destroy');
});

// Master Data
// Officer Data - Admin
Route::get('/admin/officer-data', [OfficerController::class, 'adminData'])
    ->name('admin.officer-data')
    ->middleware('role:admin,village_head');

// Officer Data - Bidan
Route::get('/midwife/officer-data', [OfficerController::class, 'midwifeData'])
    ->name('midwife.officer-data')
    ->middleware('role:admin,village_head,midwife');

// Officer Data - Tenaga Medis Puskesmas dan Kader
Route::get('/officer/officer-data', [OfficerController::class, 'officerData'])
    ->name('officer.officer-data')
    ->middleware('role:admin,village_head,officer');

// Resource Officer Data
Route::resource('/officer-data', OfficerController::class)->middleware('role:admin');

// Detail Officer Data
Route::get('/officer-data/{id}/show', [OfficerController::class, 'show'])
    ->name('officer-data.show')
    ->middleware('role:admin,village_head,midwife,officer');

// Reset Officer
Route::put('/officer-data/{id}/reset', [OfficerController::class, 'resetAccount'])
    ->name('officer-data.reset')
    ->middleware('role:admin');

// Edit Profile Officer
Route::get('/officer-profile', [OfficerController::class, 'editProfile'])
    ->name('officer-profile.editProfile')
    ->middleware('role:admin,village_head,midwife,officer');

// Update Profile Officer
Route::put('/officer-profile', [OfficerController::class, 'updateProfile'])
    ->name('officer-profile.updateProfile')
    ->middleware('role:admin,village_head,midwife,officer');

// Family Parents
// Resource Family Parents Data
Route::resource('/parent-data', FamilyParentController::class)->middleware('role:admin,village_head,midwife,officer');

// Detail Family Parent Data
Route::get('/parent-data/{id}/show', [FamilyParentController::class, 'show'])
    ->name('parent-data.show')
    ->middleware('role:admin,village_head,midwife,officer');

// Verify Family Parent
Route::put('/parent-data/{id}/verify', [FamilyParentController::class, 'verifyAccount'])
    ->name('parent-data.veryfy')
    ->middleware('role:admin');

// Reset Family Parent
Route::put('/parent-data/{id}/reset', [FamilyParentController::class, 'resetAccount'])
    ->name('parent-data.reset')
    ->middleware('role:admin');

// Edit Profile Family Parent
Route::get('/parent-profile', [FamilyParentController::class, 'editProfile'])
    ->name('parent-profile.editProfile')
    ->middleware('role:family_parent');

// Update Profile Family Parent
Route::put('/parent-profile', [FamilyParentController::class, 'updateProfile'])
    ->name('parent-profile.updateProfile')
    ->middleware('role:family_parent');

// Family Children
// Resource Family Children Data
Route::resource('/children-data', FamilyChildrenController::class)->middleware('auth');

// Detail Family Children Data
Route::get('/children-data/{id}/show', [FamilyChildrenController::class, 'show'])
    ->name('children-data.show')
    ->middleware('auth');

// Elderlies
// Resource Elderly Data
Route::resource('/elderly-data', ElderlyController::class)->middleware('role:admin,village_head,officer');

// Detail Elderly Data
Route::get('/elderly-data/{id}/show', [ElderlyController::class, 'show'])
    ->name('elderly-data.show')
    ->middleware('role:admin,village_head,officer');

// Vaccine
// Resource Vaccine Data
Route::resource('/vaccine-data', VaccineController::class)->middleware('role:admin,midwife,officer');

// Print Stock Vaccine Data
Route::get('/print-stock-report/vaccine-data', [VaccineController::class, 'printStockReport'])
    ->name('vaccine-data.print-stock')
    ->middleware('role:admin,midwife,officer');

// Medicine
// Resource Medicine Data
Route::resource('/medicine-data', MedicineController::class)->middleware('role:admin,midwife,officer');

// Print Stock Medicine Data
Route::get('/print-stock-report/medicine-data', [MedicineController::class, 'printStockReport'])
    ->name('medicine-data.print-stock')
    ->middleware('role:admin,midwife,officer');

// History Medicine Data
Route::get('/history/medicine-data', [MedicineController::class, 'historyMedicine'])
    ->name('medicine-data.history')
    ->middleware('role:admin,midwife,officer');

// Print History Medicine Data
Route::get('/print-report/medicine-data', [MedicineController::class, 'printReport'])
    ->name('medicine-data.print')
    ->middleware('role:admin,midwife,officer');

// Immunization
// Resource Immunization Data
Route::resource('/immunization-data', ImmunizationController::class)->middleware('auth');

// Detail Immunization Data
Route::get('/immunization-data/{id}/show', [ImmunizationController::class, 'show'])
    ->name('immunization-data.show')
    ->middleware('auth');

// Print Immunization Data
Route::get('/print-report/immunization-data', [ImmunizationController::class, 'printReport'])
    ->name('immunization-data.print')
    ->middleware('role:admin,village_head,midwife,officer');

// Kelola Obat Immunization Data
Route::get('/immunization-data/{id}/medicine/manage', [ImmunizationController::class, 'manageMedicine'])
    ->name('immunization-data.medicine.manage')
    ->middleware('role:admin,village_head,midwife,officer');

Route::get('/immunization-data/{id}/medicine', function ($id) {
    abort(404);
})->middleware('auth');

Route::post('/immunization-data/{id}/medicine', [ImmunizationController::class, 'storeMedicine'])
    ->name('immunization-data.medicine.store')
    ->middleware('role:admin,village_head,midwife,officer');

Route::put('/immunization-data/{id}/medicine', [ImmunizationController::class, 'updateMedicine'])
    ->name('immunization-data.medicine.update')
    ->middleware('role:admin,village_head,midwife,officer');

Route::delete('/immunization-data/{id}/medicine', [ImmunizationController::class, 'destroyMedicine'])
    ->name('immunization-data.medicine.destroy')
    ->middleware('role:admin,village_head,midwife,officer');

// Weighing
// Resource Weighing Data
Route::resource('/weighing-data', WeighingController::class)->middleware('auth');

// Print Weighing Data
Route::get('/print-report/weighing-data', [WeighingController::class, 'printReport'])
    ->name('weighing-data.print')
    ->middleware('role:admin,village_head,midwife,officer');

// Pregnancy Check
// Resource Pregnancy Check Data
Route::resource('/pregnancy-check-data', PregnancyCheckController::class)->middleware('auth');

// Detail Pregnancy Check Data
Route::get('/pregnancy-check-data/{id}/show', [PregnancyCheckController::class, 'show'])
    ->name('pregnancy-check-data.show')
    ->middleware('auth');

    // Jika menggunakan web.php
Route::get('/api/vaccines/type/{type}', [PregnancyCheckController::class, 'getVaccinesByType']);


// Print Pregnancy Check Data
Route::get('/print-report/pregnancy-check-data', [PregnancyCheckController::class, 'printReport'])
    ->name('pregnancy-check-data.print')
    ->middleware('role:admin,village_head,midwife,officer');

// Kelola Obat Pregnancy Check Data
Route::get('/pregnancy-check-data/{id}/medicine/manage', [PregnancyCheckController::class, 'manageMedicine'])
    ->name('pregnancy-check-data.medicine.manage')
    ->middleware('role:admin,village_head,midwife,officer');

Route::get('/pregnancy-check-data/{id}/medicine', function ($id) {
    abort(404);
})->middleware('auth');

Route::post('/pregnancy-check-data/{id}/medicine', [PregnancyCheckController::class, 'storeMedicine'])
    ->name('pregnancy-check-data.medicine.store')
    ->middleware('role:admin,village_head,midwife,officer');

Route::put('/pregnancy-check-data/{id}/medicine', [PregnancyCheckController::class, 'updateMedicine'])
    ->name('pregnancy-check-data.medicine.update')
    ->middleware('role:admin,village_head,midwife,officer');

Route::delete('/pregnancy-check-data/{id}/medicine', [PregnancyCheckController::class, 'destroyMedicine'])
    ->name('pregnancy-check-data.medicine.destroy')
    ->middleware('role:admin,village_head,midwife,officer');

// Elderly Check
// Resource Elderly Check Data
Route::resource('/elderly-check-data', ElderlyCheckController::class)->middleware('auth');

// Detail Edelry Check Data
Route::get('/elderly-check-data/{id}/show', [ElderlyCheckController::class, 'show'])
    ->name('elderly-check-data.show')
    ->middleware('role:admin,village_head,midwife,officer');

// Print Elderly Check Data
Route::get('/print-report/elderly-check-data', [ElderlyCheckController::class, 'printReport'])
    ->name('elderly-check-data.print')
    ->middleware('role:admin,village_head,midwife,officer');

// Kelola Obat Elderly Check Data
Route::get('/elderly-check-data/{id}/medicine/manage', [ElderlyCheckController::class, 'manageMedicine'])
    ->name('elderly-check-data.medicine.manage')
    ->middleware('role:admin,village_head,midwife,officer');

Route::get('/elderly-check-data/{id}/medicine', function ($id) {
    abort(404);
})->middleware('auth');

Route::post('/elderly-check-data/{id}/medicine', [ElderlyCheckController::class, 'storeMedicine'])
    ->name('elderly-check-data.medicine.store')
    ->middleware('role:admin,village_head,midwife,officer');

Route::put('/elderly-check-data/{id}/medicine', [ElderlyCheckController::class, 'updateMedicine'])
    ->name('elderly-check-data.medicine.update')
    ->middleware('role:admin,village_head,midwife,officer');

Route::delete('/elderly-check-data/{id}/medicine', [ElderlyCheckController::class, 'destroyMedicine'])
    ->name('elderly-check-data.medicine.destroy')
    ->middleware('role:admin,village_head,midwife,officer');

// Schedule
// Resource Schedule
Route::resource('/schedule', EventScheduleController::class)->middleware('auth');

// Detail Schedule
Route::get('/schedule/{id}/show', [EventScheduleController::class, 'show'])
    ->name('schedule.show')->middleware('auth');

// Edit Site Identity
Route::get('/site-identity', [SiteIdentityController::class, 'index'])
    ->name('site-identity.index')
    ->middleware('role:admin');

// Update Site Identity
Route::put('/site-identity', [SiteIdentityController::class, 'update'])
    ->name('site-identity.update')
    ->middleware('role:admin');

// AJAX
Route::get('/schedule/ajax/{day}', [EventScheduleController::class, 'getScheduleByDay'])
    ->middleware('auth');

Route::get('/immunization-data/ajax/{year}', [ImmunizationController::class, 'getImmunizationStatistics'])
    ->middleware('auth');

Route::get('/weighing-data/ajax/{year}', [WeighingController::class, 'getWeighingStatistics'])
    ->middleware('auth');

Route::get('/pregnancy-check-data/ajax/{year}', [PregnancyCheckController::class, 'getPregnancyCheckStatistics'])
    ->middleware('auth');

Route::get('/elderly-check-data/ajax/{year}', [ElderlyCheckController::class, 'getElderlyCheckStatistics'])
    ->middleware('auth');

Route::get('/nutrition-status/ajax/{year}', [WeighingController::class, 'getNutritionStatusStatistics'])
    ->middleware('auth');

Route::get('/children-nutrition-status/ajax/{year?}', [WeighingController::class, 'getNutritionStatusParentChildrenStatistics'])
    ->middleware('role:family_parent');

Route::get('/get-unverified-parents', [FamilyParentController::class, 'getUnverifiedParents'])->name('get.unverified.parents')
    ->middleware('role:admin');

// Articles
Route::resource('dashboard/articles', ArticleController::class)
    ->middleware(['auth', 'role:admin,officer,village_head'])
    ->names([
        'index' => 'articles.index',
        'create' => 'articles.create',
        'store' => 'articles.store',
        'edit' => 'articles.edit',
        'update' => 'articles.update',
        'destroy' => 'articles.destroy',
    ]);

    Route::get('/schedule/ajax/month/{month}', [DashboardController::class, 'getSchedulesByMonth']);


// Route::resource('dashboard/pendaftaran', PendaftaranController::class)
//     ->middleware(['auth', 'role:admin,officer,village_head'])
//     ->names([
//         'index' => 'pendaftaran.index',
//         'create' => 'pendaftaran.create',
//         'store' => 'pendaftaran.store',
//         'edit' => 'pendaftaran.edit',
//         'update' => 'pendaftaran.update',
//         'destroy' => 'pendaftaran.destroy',
//     ]);

    // Tambahkan routes ini
Route::resource('pendaftaran', PendaftaranController::class);
Route::patch('pendaftaran/{id}/update-status', [PendaftaranController::class, 'updateStatus'])->name('pendaftaran.updateStatus');
Route::get('pendaftaran-print', [PendaftaranController::class, 'print'])->name('pendaftaran.print');
Route::get('api/children-by-parent/{parentId}', [PendaftaranController::class, 'getChildrenByParent']);


// Print Family Children Data
// Add this line with your other children routes
Route::get('/cetak', [App\Http\Controllers\Dashboard\FamilyChildrenController::class, 'printData'])->name('children.print');

Route::get('/print', [FamilyParentController::class, 'printReport'])->name('parent-data.print');
