<?php

use App\Controllers\Api\Auth\Forgot;
use App\Controllers\Api\Auth\GoogleLogin;
use App\Controllers\Api\Auth\Login;
use App\Controllers\Api\Auth\Refresh;
use App\Controllers\Api\Auth\Registration;
use App\Controllers\Api\Auth\Reset;
use App\Controllers\Api\Bridge\AppData;
use App\Controllers\Api\Configuration;
use App\Controllers\Api\CreatePackage;
use App\Controllers\Api\Customer\Payment\CheckCancelProvider;
use App\Controllers\Api\Customer\Payment\CheckProvider;
use App\Controllers\Api\Customer\Payment\Stripe\CancelSubscribe;
use App\Controllers\Api\Customer\Payment\Stripe\CreatePaymentRequest;
use App\Controllers\Api\Customer\Payment\Razorpay\CreatePaymentRequest as RazorCreatePaymentRequest;
use App\Controllers\Api\Customer\Payment\Razorpay\CancelSubscribe as RazorCancelSubscribe;
use App\Controllers\Api\Customer\Payment\YooKassa\CreatePaymentRequest as YooKassaCreatePaymentRequest;
use App\Controllers\Api\Customer\Payment\YooKassa\CancelSubscribe as YooKassaCancelSubscribe;
use App\Controllers\Api\Customer\Payment\ZarinPal\CreatePaymentRequest as ZarinPalCreatePaymentRequest;
use App\Controllers\Api\Customer\Plans\PlansList;
use App\Controllers\Api\Customer\Profile\ProfileDetail;
use App\Controllers\Api\Customer\Profile\Subscribe\Subscribes;
use App\Controllers\Api\Customer\Profile\Subscribe\Transactions;
use App\Controllers\Api\Customer\Profile\UpdatePassword;
use App\Controllers\Api\Customer\Projects\Builds\BuildsList;
use App\Controllers\Api\Customer\Projects\Builds\CreateBuild;
use App\Controllers\Api\Customer\Projects\Builds\DownloadArtefact;
use App\Controllers\Api\Customer\Projects\CreateProject;
use App\Controllers\Api\Customer\Projects\DeleteProject;
use App\Controllers\Api\Customer\Projects\SaveConfig;
use App\Controllers\Api\Customer\Projects\GetConfig;
use App\Controllers\Api\Customer\Projects\Design\CreateStyleDiv;
use App\Controllers\Api\Customer\Projects\Design\DrawerSettings;
use App\Controllers\Api\Customer\Projects\Design\RemoveStyleDiv;
use App\Controllers\Api\Customer\Projects\Design\StylesList;
use App\Controllers\Api\Customer\Projects\Design\TemplateSettings;
use App\Controllers\Api\Customer\Projects\Design\UpdateDrawer;
use App\Controllers\Api\Customer\Projects\Design\UpdateDrawerBackground;
use App\Controllers\Api\Customer\Projects\Design\UpdateDrawerLogo;
use App\Controllers\Api\Customer\Projects\Design\UpdateTemplate;
use App\Controllers\Api\Customer\Projects\Icon\DownloadIcon;
use App\Controllers\Api\Customer\Projects\Icon\LaunchIconDetail;
use App\Controllers\Api\Customer\Projects\Icon\UploadIcon;
use App\Controllers\Api\Customer\Projects\Localization\LocalizationList;
use App\Controllers\Api\Customer\Projects\Localization\RefreshLocalization;
use App\Controllers\Api\Customer\Projects\Localization\UpdateLocalization;
use App\Controllers\Api\Customer\Projects\Localization\UploadErrorImage;
use App\Controllers\Api\Customer\Projects\Localization\UploadOfflineImage;
use App\Controllers\Api\Customer\Projects\Navigation\BarNavigationList;
use App\Controllers\Api\Customer\Projects\Navigation\CreateBarNav;
use App\Controllers\Api\Customer\Projects\Navigation\CreateMainNav;
use App\Controllers\Api\Customer\Projects\Navigation\MainNavigationList;
use App\Controllers\Api\Customer\Projects\Navigation\RemoveBarNav;
use App\Controllers\Api\Customer\Projects\Navigation\RemoveMainNav;
use App\Controllers\Api\Customer\Projects\Navigation\UpdateBarNav;
use App\Controllers\Api\Customer\Projects\Navigation\UpdateMainNav;
use App\Controllers\Api\Customer\Projects\Newsletter\CreateNotification;
use App\Controllers\Api\Customer\Projects\Newsletter\NotificationsList;
use App\Controllers\Api\Customer\Projects\Newsletter\TotalSubscribers;
use App\Controllers\Api\Customer\Projects\Permissions\PermissionsDetail;
use App\Controllers\Api\Customer\Projects\Permissions\UpdatePermissions;
use App\Controllers\Api\Customer\Projects\Preview\Snack;
use App\Controllers\Api\Customer\Projects\ProjectDetail;
use App\Controllers\Api\Customer\Projects\ProjectsList;
use App\Controllers\Api\Customer\Projects\Settings\AppSettings;
use App\Controllers\Api\Customer\Projects\Settings\UpdateAppSettings;
use App\Controllers\Api\Customer\Projects\Signing\CreateAndroidSignature;
use App\Controllers\Api\Customer\Projects\Signing\RemoveAndroidSignature;
use App\Controllers\Api\Customer\Projects\Signing\RemoveIosSignature;
use App\Controllers\Api\Customer\Projects\Signing\SignaturesList;
use App\Controllers\Api\Customer\Projects\Signing\SignaturesShortList;
use App\Controllers\Api\Customer\Projects\Signing\UploadAndroidSignature;
use App\Controllers\Api\Customer\Projects\Signing\UploadIosSignature;
use App\Controllers\Api\Customer\Projects\Splashscreen\SplashscreenDetail;
use App\Controllers\Api\Customer\Projects\Splashscreen\UpdateSplashscreen;
use App\Controllers\Api\Customer\Projects\Splashscreen\UploadSplashBackground;
use App\Controllers\Api\Customer\Projects\Splashscreen\UploadSplashLogo;
use App\Controllers\Api\Customer\Support\ChangeRating;
use App\Controllers\Api\Customer\Support\ChangeTicketStatus;
use App\Controllers\Api\Customer\Support\CreateComment;
use App\Controllers\Api\Customer\Support\CreateTicket;
use App\Controllers\Api\Customer\Support\TicketDetail;
use App\Controllers\Api\Customer\Support\TicketsList;
use App\Controllers\Api\Ipn\Razorpay;
use App\Controllers\Api\Ipn\ZarinPal as IpnZarinPal;
use App\Controllers\Api\Ipn\YooKassa;
use App\Controllers\Api\Manager\Projects\ProjectsList as AdminProjectsList;
use App\Controllers\Api\Manager\Projects\ProjectsUserList as AdminProjectsUserList;
use App\Controllers\Api\Manager\Projects\ProjectDetail as AdminProjectDetail;
use App\Controllers\Api\Manager\Projects\DeleteProject as AdminProjectDelete;
use App\Controllers\Api\Manager\Projects\Settings\AppSettings as AdminAppSettings;
use App\Controllers\Api\Manager\Projects\Settings\UpdateAppSettings as AdminUpdateAppSettings;
use App\Controllers\Api\Manager\Projects\Design\StylesList as AdminStylesList;
use App\Controllers\Api\Manager\Projects\Design\CreateStyleDiv as AdminCreateStyleDiv;
use App\Controllers\Api\Manager\Projects\Design\RemoveStyleDiv as AdminRemoveStyleDiv;
use App\Controllers\Api\Manager\Projects\Design\DrawerSettings as AdminDrawerSettings;
use App\Controllers\Api\Manager\Projects\Design\UpdateDrawer as AdminUpdateDrawer;
use App\Controllers\Api\Manager\Projects\Design\UpdateDrawerBackground as AdminUpdateDrawerBackground;
use App\Controllers\Api\Manager\Projects\Design\UpdateDrawerLogo as AdminUpdateDrawerLogo;
use App\Controllers\Api\Manager\Projects\Design\TemplateSettings as AdminTemplateSettings;
use App\Controllers\Api\Manager\Projects\Design\UpdateTemplate as AdminUpdateTemplate;
use App\Controllers\Api\Manager\Projects\Permissions\PermissionsDetail as AdminPermissionsDetail;
use App\Controllers\Api\Manager\Projects\Permissions\UpdatePermissions as AdminUpdatePermissions;
use App\Controllers\Api\Manager\Projects\Navigation\BarNavigationList as AdminBarNavigationList;
use App\Controllers\Api\Manager\Projects\Navigation\CreateBarNav as AdminCreateBarNav;
use App\Controllers\Api\Manager\Projects\Navigation\CreateMainNav as AdminCreateMainNav;
use App\Controllers\Api\Manager\Projects\Navigation\MainNavigationList as AdminMainNavigationList;
use App\Controllers\Api\Manager\Projects\Navigation\RemoveBarNav as AdminRemoveBarNav;
use App\Controllers\Api\Manager\Projects\Navigation\RemoveMainNav as AdminRemoveMainNav;
use App\Controllers\Api\Manager\Projects\Navigation\UpdateBarNav as AdminUpdateBarNav;
use App\Controllers\Api\Manager\Projects\Navigation\UpdateMainNav as AdminUpdateMainNav;
use App\Controllers\Api\Manager\Projects\Localization\LocalizationList as AdminLocalizationList;
use App\Controllers\Api\Manager\Projects\Localization\RefreshLocalization as AdminRefreshLocalization;
use App\Controllers\Api\Manager\Projects\Localization\UpdateLocalization as AdminUpdateLocalization;
use App\Controllers\Api\Manager\Projects\Localization\UploadErrorImage as AdminUploadErrorImage;
use App\Controllers\Api\Manager\Projects\Localization\UploadOfflineImage as AdminUploadOfflineImage;
use App\Controllers\Api\Manager\Projects\Splashscreen\SplashscreenDetail as AdminSplashscreenDetail;
use App\Controllers\Api\Manager\Projects\Splashscreen\UpdateSplashscreen as AdminUpdateSplashscreen;
use App\Controllers\Api\Manager\Projects\Splashscreen\UploadSplashBackground as AdminUploadSplashBackground;
use App\Controllers\Api\Manager\Projects\Splashscreen\UploadSplashLogo as AdminUploadSplashLogo;
use App\Controllers\Api\Manager\Projects\Icon\DownloadIcon as AdminDownloadIcon;
use App\Controllers\Api\Manager\Projects\Icon\LaunchIconDetail as AdminLaunchIconDetail;
use App\Controllers\Api\Manager\Projects\Icon\UploadIcon as AdminUploadIcon;
use App\Controllers\Api\Manager\Projects\Builds\BuildsList as AdminBuildsList;
use App\Controllers\Api\Manager\Projects\Builds\CreateBuild as AdminCreateBuild;
use App\Controllers\Api\Manager\Projects\Builds\DownloadArtefact as AdminDownloadArtefact;
use App\Controllers\Api\Manager\Projects\Signing\CreateAndroidSignature as AdminCreateAndroidSignature;
use App\Controllers\Api\Manager\Projects\Signing\RemoveAndroidSignature as AdminRemoveAndroidSignature;
use App\Controllers\Api\Manager\Projects\Signing\RemoveIosSignature as AdminRemoveIosSignature;
use App\Controllers\Api\Manager\Projects\Signing\SignaturesList as AdminSignaturesList;
use App\Controllers\Api\Manager\Projects\Signing\SignaturesShortList as AdminSignaturesShortList;
use App\Controllers\Api\Manager\Projects\Signing\UploadAndroidSignature as AdminUploadAndroidSignature;
use App\Controllers\Api\Manager\Projects\Signing\UploadIosSignature as AdminUploadIosSignature;
use App\Controllers\Api\Manager\Projects\Newsletter\CreateNotification as AdminCreateNotification;
use App\Controllers\Api\Manager\Projects\Newsletter\NotificationsList as AdminNotificationsList;
use App\Controllers\Api\Manager\Projects\Newsletter\TotalSubscribers as AdminTotalSubscribers;
use App\Controllers\Api\Manager\Support\ChangeTicketStatus as AdminChangeTicketStatus;
use App\Controllers\Api\Manager\Support\CreateComment as AdminCreateComment;
use App\Controllers\Api\Manager\Support\TicketDetail as AdminTicketDetail;
use App\Controllers\Api\Manager\Support\TicketsList as AdminTicketsList;
use App\Controllers\Api\Manager\Support\TicketsUserList as AdminTicketsUserList;
use App\Controllers\Api\Manager\Transactions\Transactions as AdminTransactions;
use App\Controllers\Api\Manager\Transactions\Subscribe as AdminSubscribe;
use App\Controllers\Api\Manager\Transactions\UserSubscribes as AdminUserSubscribes;
use App\Controllers\Api\Manager\Users\UsersList as AdminUsersList;
use App\Controllers\Api\Manager\Users\UserDetail as AdminUserDetail;
use App\Controllers\Api\Manager\Users\UpdateUser as AdminUpdateUser;
use App\Controllers\Api\Manager\Providers\ProvidersList as AdminProvidersList;
use App\Controllers\Api\Manager\Providers\UpdateProvider as AdminUpdateProvider;
use App\Controllers\Api\Manager\Plans\PlansList as AdminPlansList;
use App\Controllers\Api\Manager\Plans\CreatePlan as AdminCreatePlan;
use App\Controllers\Api\Manager\Plans\UpdatePlan as AdminUpdatePlan;
use App\Controllers\Api\Manager\Plans\RemovePlan as AdminRemovePlan;
use App\Controllers\Api\Manager\Settings\SiteSettings as AdminSiteSettings;
use App\Controllers\Api\Manager\Settings\UpdateSiteSettings as AdminUpdateSiteSettings;
use App\Controllers\Api\Manager\Settings\UploadLogo as AdminUploadLogo;
use App\Controllers\Api\Manager\Settings\ExternalApiSettings as AdminExternalApiSettings;
use App\Controllers\Api\Manager\Settings\UpdateApiSettings as AdminUpdateApiSettings;
use App\Controllers\Api\Manager\Settings\OneSignalSettings as AdminOneSignalSettings;
use App\Controllers\Api\Manager\Settings\UpdateOneSignal as AdminUpdateOneSignal;
use App\Controllers\Api\Manager\Settings\UploadFcm as AdminUploadFcm;
use App\Controllers\Api\Manager\Settings\EmailSettings as AdminEmailSettings;
use App\Controllers\Api\Manager\Settings\UpdateEmailSettings as AdminUpdateEmailSettings;
use App\Controllers\Api\Manager\Settings\LicenseSettings as AdminLicenseSettings;
use App\Controllers\Api\Manager\Settings\ActivateLicense as AdminActivateLicense;
use App\Controllers\Api\Manager\Settings\ZarinPalSettings as AdminZarinPalSettings;
use App\Controllers\Api\Manager\Settings\UpdateZarinPalSettings as AdminUpdateZarinPalSettings;
use App\Controllers\Api\Manager\Settings\EnamadSettings as AdminEnamadSettings;
use App\Controllers\Api\Manager\Settings\UpdateEnamadSettings as AdminUpdateEnamadSettings;
use App\Controllers\Api\Manager\Settings\LandingSettings as AdminLandingSettings;
use App\Controllers\Api\Manager\Settings\UpdateLandingSettings as AdminUpdateLandingSettings;
use App\Controllers\Api\Manager\Dashboard\TotalStat as AdminTotalStat;
use App\Controllers\Api\Manager\Dashboard\ChartStat as AdminChartStat;
use App\Controllers\Api\Manager\Templates\TemplatesList as AdminTemplatesList;
use App\Controllers\Api\Manager\Templates\TemplateDetail as AdminTemplateDetail;
use App\Controllers\Api\Manager\Templates\CreateTemplate as AdminCreateTemplate;
use App\Controllers\Api\Manager\Templates\UpdateTemplate as AdminUpdateTemplate;
use App\Controllers\Api\Manager\Templates\DeleteTemplate as AdminDeleteTemplate;
use App\Controllers\Api\Manager\Templates\UpdateFormSchema as AdminUpdateFormSchema;
use App\Controllers\Api\Manager\Templates\UploadThumbnail as AdminUploadThumbnail;
use App\Controllers\Api\Manager\Templates\CategoriesList as AdminCategoriesList;
use App\Controllers\Api\Manager\Languages\LanguagesList as AdminLanguagesList;
use App\Controllers\Api\Manager\Languages\UpdateLanguage as AdminUpdateLanguage;
use App\Controllers\Api\Manager\Languages\TranslationsList as AdminTranslationsList;
use App\Controllers\Api\Manager\Languages\UpdateTranslation as AdminUpdateTranslation;
use App\Controllers\Api\Manager\Languages\BulkUpdateTranslations as AdminBulkUpdateTranslations;
use App\Controllers\Api\Data\Icons;
use App\Controllers\Api\Data\Templates as DataTemplates;
use App\Controllers\Api\Data\Translations as DataTranslations;
use App\Controllers\Api\Data\SiteSettings as DataSiteSettings;
use App\Controllers\Api\Data\Iso;
use App\Controllers\Api\Ipn\Stripe;
use App\Controllers\Api\Observe;
use App\Controllers\Install\BaseConnection;
use App\Controllers\Install\CreateAdmin;
use App\Controllers\Install\GitConnection;
use App\Controllers\Install\Pages;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * Install methods
 * --------------------------------------------------------------------
 *
 * Public methods without authorization headers
 */

$routes->group("install", static function ($routes) {
    $routes->get("step/1",        [Pages::class, "step_1"], []);
    $routes->get("step/2",        [Pages::class, "step_2"], []);
    $routes->get("step/3",        [Pages::class, "step_3"], []);
    $routes->get("step/4",        [Pages::class, "step_4"], []);
    $routes->post("connect_db",   [BaseConnection::class, "index"], []);
    $routes->post("connect_git",  [GitConnection::class, "index"], []);
    $routes->post("create_admin", [CreateAdmin::class, "index"], []);
});

/*
 * --------------------------------------------------------------------
 * Public methods
 * --------------------------------------------------------------------
 *
 * Public API methods without authorization headers
 */
$routes->group("public", static function ($routes) {
    // Settings
    $routes->get("settings/config",     [Configuration::class, "initial"], []);
    // Data
    $routes->get("data/iso",            [Iso::class, "index"], []);
    $routes->get("data/icons",          [Icons::class, "index"], []);
    $routes->get("data/templates",      [DataTemplates::class, "index"], []);
    $routes->get("data/templates/(:segment)", [DataTemplates::class, "detail"], []);
    $routes->get("data/categories",     [DataTemplates::class, "categories"], []);
    $routes->get("data/translations",   [DataTranslations::class, "index"], []);
    $routes->get("data/languages",      [DataTranslations::class, "languages"], []);
    $routes->get("data/site_settings",  [DataSiteSettings::class, "index"], []);
    // Auth
    $routes->post("auth/login",         [Login::class, "index"], []);
    $routes->post("auth/sign_up",       [Registration::class, "index"], []);
    $routes->post("auth/forgot",        [Forgot::class, "index"], []);
    $routes->post("auth/reset",         [Reset::class, "index"], []);
    $routes->post("auth/google",        [GoogleLogin::class, "index"], []);
    $routes->post("auth/refresh",       [Refresh::class, "index"], []);
    // Bridge
    $routes->get("bridge/app",          [AppData::class, "index"], []);
    // Package handle
    $routes->get("bundle/create",       [CreatePackage::class, "index"], []);
    $routes->get("observe/notice",      [Observe::class, "index"], []);
    // Ipn
    $routes->post("ipn/stripe",         [Stripe::class, "index"], []);
    $routes->post("ipn/razorpay",       [Razorpay::class, "index"], []);
    $routes->get("ipn/zarinpal",        [IpnZarinPal::class, "index"], []);
    $routes->post("ipn/yookassa",       [YooKassa::class, "index"], []);
    $routes->post("ipn/yookassa/check", [YooKassa::class, "payment"], []);
});

/*
 * --------------------------------------------------------------------
 * Private methods
 * --------------------------------------------------------------------
 *
 * Private API methods with authorization headers
 */
$routes->group("private", ["filter" => "private"], static function ($routes) {
    // Profile
    $routes->get("profile/main",                                [ProfileDetail::class, "index"], []);
    $routes->get("profile/subscribes",                          [Subscribes::class, "index"], []);
    $routes->get("profile/transactions",                        [Transactions::class, "index"], []);
    $routes->post("profile/update_password",                    [UpdatePassword::class, "index"], []);
    // Plans
    $routes->get("plans/list",                                  [PlansList::class, "index"], []);
    // Payment
    $routes->get("payment/method",                              [CheckProvider::class, "index"], []);
    $routes->get("payment/cancel",                              [CheckCancelProvider::class, "index"], []);
    $routes->post("payment/stripe",                             [CreatePaymentRequest::class, "index"], []);
    $routes->post("cancel/stripe",                              [CancelSubscribe::class, "index"], []);
    $routes->post("payment/razorpay",                           [RazorCreatePaymentRequest::class, "index"], []);
    $routes->post("cancel/razorpay",                            [RazorCancelSubscribe::class, "index"], []);
    $routes->post("payment/yookassa",                           [YooKassaCreatePaymentRequest::class, "index"], []);
    $routes->post("cancel/yookassa",                            [YooKassaCancelSubscribe::class, "index"], []);
    $routes->post("payment/zarinpal",                           [ZarinPalCreatePaymentRequest::class, "index"], []);
    // Support
    $routes->get("support/tickets",                             [TicketsList::class, "index"], []);
    $routes->get("support/ticket",                              [TicketDetail::class, "index"], []);
    $routes->post("support/create",                             [CreateTicket::class, "index"], []);
    $routes->post("support/create_comment",                     [CreateComment::class, "index"], []);
    $routes->post("support/change_status",                      [ChangeTicketStatus::class, "index"], []);
    $routes->post("support/change_rating",                      [ChangeRating::class, "index"], []);
    // Projects (apps)
    $routes->get("projects/list",                               [ProjectsList::class, "index"], []);
    $routes->get("projects/detail",                             [ProjectDetail::class, "index"], []);
    $routes->post("projects/create",                            [CreateProject::class, "index"], []);
    $routes->post("projects/remove",                            [DeleteProject::class, "index"], []);
    $routes->get("projects/config",                             [GetConfig::class, "index"], []);
    $routes->post("projects/config/save",                       [SaveConfig::class, "index"], []);
    // Project settings
    $routes->get("projects/settings/main",                      [AppSettings::class, "index"], []);
    $routes->post("projects/settings/update_main",              [UpdateAppSettings::class, "index"], []);
    // Project design settings
    $routes->get("projects/settings/template",                  [TemplateSettings::class, "index"], []);
    $routes->post("projects/settings/template_update",          [UpdateTemplate::class, "index"], []);
    $routes->get("projects/settings/styles",                    [StylesList::class, "index"], []);
    $routes->post("projects/settings/remove_div",               [RemoveStyleDiv::class, "index"], []);
    $routes->post("projects/settings/create_div",               [CreateStyleDiv::class, "index"], []);
    // Project drawer settings
    $routes->get("projects/settings/drawer",                    [DrawerSettings::class, "index"], []);
    $routes->post("projects/settings/update_drawer",            [UpdateDrawer::class, "index"], []);
    $routes->post("projects/settings/update_drawer_background", [UpdateDrawerBackground::class, "index"], []);
    $routes->post("projects/settings/update_drawer_logo",       [UpdateDrawerLogo::class, "index"], []);
    // Project permissions settings
    $routes->get("projects/settings/permissions",               [PermissionsDetail::class, "index"], []);
    $routes->post("projects/settings/update_permissions",       [UpdatePermissions::class, "index"], []);
    // Project localization
    $routes->get("projects/settings/localization",              [LocalizationList::class, "index"], []);
    $routes->post("projects/settings/update_localization",      [UpdateLocalization::class, "index"], []);
    $routes->post("projects/settings/refresh_localization",     [RefreshLocalization::class, "index"], []);
    $routes->post("projects/settings/update_offline_img",       [UploadOfflineImage::class, "index"], []);
    $routes->post("projects/settings/update_error_img",         [UploadErrorImage::class, "index"], []);
    // Project splashscreen
    $routes->get("projects/settings/splashscreen",              [SplashscreenDetail::class, "index"], []);
    $routes->post("projects/settings/update_splashscreen",      [UpdateSplashscreen::class, "index"], []);
    $routes->post("projects/settings/upload_splash_background", [UploadSplashBackground::class, "index"], []);
    $routes->post("projects/settings/upload_splash_logo",       [UploadSplashLogo::class, "index"], []);
    // Project launch icon
    $routes->get("projects/settings/launch_icon",               [LaunchIconDetail::class, "index"], []);
    $routes->post("projects/settings/download_icon",            [DownloadIcon::class, "index"], []);
    $routes->post("projects/settings/upload_icon",              [UploadIcon::class, "index"], []);
    // Project main navigation
    $routes->get("projects/settings/main_navigation",           [MainNavigationList::class, "index"], []);
    $routes->post("projects/settings/create_main_nav",          [CreateMainNav::class, "index"], []);
    $routes->post("projects/settings/update_main_nav",          [UpdateMainNav::class, "index"], []);
    $routes->post("projects/settings/remove_main_nav",          [RemoveMainNav::class, "index"], []);
    // Project bar navigation
    $routes->get("projects/settings/bar_navigation",            [BarNavigationList::class, "index"], []);
    $routes->post("projects/settings/create_bar_nav",           [CreateBarNav::class, "index"], []);
    $routes->post("projects/settings/update_bar_nav",           [UpdateBarNav::class, "index"], []);
    $routes->post("projects/settings/remove_bar_nav",           [RemoveBarNav::class, "index"], []);
    // Builds
    $routes->get("projects/builds/list",                        [BuildsList::class, "index"], []);
    $routes->post("projects/builds/create",                     [CreateBuild::class, "index"], []);
    $routes->post("projects/builds/download",                   [DownloadArtefact::class, "index"], []);
    // Signs
    $routes->get("projects/signatures/short_list",              [SignaturesShortList::class, "index"], []);
    $routes->get("projects/signatures/list",                    [SignaturesList::class, "index"], []);
    $routes->post("projects/signatures/create_android",         [CreateAndroidSignature::class, "index"], []);
    $routes->post("projects/signatures/upload_android",         [UploadAndroidSignature::class, "index"], []);
    $routes->post("projects/signatures/upload_ios",             [UploadIosSignature::class, "index"], []);
    $routes->post("projects/signatures/remove_android",         [RemoveAndroidSignature::class, "index"], []);
    $routes->post("projects/signatures/remove_ios",             [RemoveIosSignature::class, "index"], []);
    // Newsletter
    $routes->get("projects/newsletter/subscribes",              [TotalSubscribers::class, "index"], []);
    $routes->get("projects/newsletter/notifications",           [NotificationsList::class, "index"], []);
    $routes->post("projects/newsletter/create",                 [CreateNotification::class, "index"], []);
    // App preview
    $routes->post("projects/preview/launch",                    [Snack::class, "index"], []);
    $routes->post("projects/preview/config",                    [Snack::class, "config"], []);
});

/*
 * --------------------------------------------------------------------
 * Admin methods
 * --------------------------------------------------------------------
 *
 * Private API methods with authorization headers and admin permission
 */
$routes->group("admin", ["filter" => "admin"], static function ($routes) {
    // Projects (apps)
    $routes->get("projects/list",                               [AdminProjectsList::class, "index"], []);
    $routes->get("projects/user_list",                          [AdminProjectsUserList::class, "index"], []);
    $routes->get("projects/detail",                             [AdminProjectDetail::class, "index"], []);
    $routes->post("projects/remove",                            [AdminProjectDelete::class, "index"], []);
    // Project settings
    $routes->get("projects/settings/main",                      [AdminAppSettings::class, "index"], []);
    $routes->post("projects/settings/update_main",              [AdminUpdateAppSettings::class, "index"], []);
    // Project design
    $routes->get("projects/settings/template",                  [AdminTemplateSettings::class, "index"], []);
    $routes->post("projects/settings/template_update",          [AdminUpdateTemplate::class, "index"], []);
    $routes->get("projects/settings/styles",                    [AdminStylesList::class, "index"], []);
    $routes->post("projects/settings/create_div",               [AdminCreateStyleDiv::class, "index"], []);
    $routes->post("projects/settings/remove_div",               [AdminRemoveStyleDiv::class, "index"], []);
    // Project drawer settings
    $routes->get("projects/settings/drawer",                    [AdminDrawerSettings::class, "index"], []);
    $routes->post("projects/settings/update_drawer",            [AdminUpdateDrawer::class, "index"], []);
    $routes->post("projects/settings/update_drawer_background", [AdminUpdateDrawerBackground::class, "index"], []);
    $routes->post("projects/settings/update_drawer_logo",       [AdminUpdateDrawerLogo::class, "index"], []);
    // Project permissions settings
    $routes->get("projects/settings/permissions",               [AdminPermissionsDetail::class, "index"], []);
    $routes->post("projects/settings/update_permissions",       [AdminUpdatePermissions::class, "index"], []);
    // Project main navigation
    $routes->get("projects/settings/main_navigation",           [AdminMainNavigationList::class, "index"], []);
    $routes->post("projects/settings/create_main_nav",          [AdminCreateMainNav::class, "index"], []);
    $routes->post("projects/settings/update_main_nav",          [AdminUpdateMainNav::class, "index"], []);
    $routes->post("projects/settings/remove_main_nav",          [AdminRemoveMainNav::class, "index"], []);
    // Project bar navigation
    $routes->get("projects/settings/bar_navigation",            [AdminBarNavigationList::class, "index"], []);
    $routes->post("projects/settings/create_bar_nav",           [AdminCreateBarNav::class, "index"], []);
    $routes->post("projects/settings/update_bar_nav",           [AdminUpdateBarNav::class, "index"], []);
    $routes->post("projects/settings/remove_bar_nav",           [AdminRemoveBarNav::class, "index"], []);
    // Project localization
    $routes->get("projects/settings/localization",              [AdminLocalizationList::class, "index"], []);
    $routes->post("projects/settings/update_localization",      [AdminUpdateLocalization::class, "index"], []);
    $routes->post("projects/settings/refresh_localization",     [AdminRefreshLocalization::class, "index"], []);
    $routes->post("projects/settings/update_offline_img",       [AdminUploadOfflineImage::class, "index"], []);
    $routes->post("projects/settings/update_error_img",         [AdminUploadErrorImage::class, "index"], []);
    // Project splashscreen
    $routes->get("projects/settings/splashscreen",              [AdminSplashscreenDetail::class, "index"], []);
    $routes->post("projects/settings/update_splashscreen",      [AdminUpdateSplashscreen::class, "index"], []);
    $routes->post("projects/settings/upload_splash_background", [AdminUploadSplashBackground::class, "index"], []);
    $routes->post("projects/settings/upload_splash_logo",       [AdminUploadSplashLogo::class, "index"], []);
    // Project launch icon
    $routes->get("projects/settings/launch_icon",               [AdminLaunchIconDetail::class, "index"], []);
    $routes->post("projects/settings/download_icon",            [AdminDownloadIcon::class, "index"], []);
    $routes->post("projects/settings/upload_icon",              [AdminUploadIcon::class, "index"], []);
    // Builds
    $routes->get("projects/builds/list",                        [AdminBuildsList::class, "index"], []);
    $routes->post("projects/builds/create",                     [AdminCreateBuild::class, "index"], []);
    $routes->post("projects/builds/download",                   [AdminDownloadArtefact::class, "index"], []);
    // Signs
    $routes->get("projects/signatures/short_list",              [AdminSignaturesShortList::class, "index"], []);
    $routes->get("projects/signatures/list",                    [AdminSignaturesList::class, "index"], []);
    $routes->post("projects/signatures/create_android",         [AdminCreateAndroidSignature::class, "index"], []);
    $routes->post("projects/signatures/upload_android",         [AdminUploadAndroidSignature::class, "index"], []);
    $routes->post("projects/signatures/upload_ios",             [AdminUploadIosSignature::class, "index"], []);
    $routes->post("projects/signatures/remove_android",         [AdminRemoveAndroidSignature::class, "index"], []);
    $routes->post("projects/signatures/remove_ios",             [AdminRemoveIosSignature::class, "index"], []);
    // Newsletter
    $routes->get("projects/newsletter/subscribes",              [AdminTotalSubscribers::class, "index"], []);
    $routes->get("projects/newsletter/notifications",           [AdminNotificationsList::class, "index"], []);
    $routes->post("projects/newsletter/create",                 [AdminCreateNotification::class, "index"], []);
    // Support
    $routes->get("support/tickets",                             [AdminTicketsList::class, "index"], []);
    $routes->get("support/user_tickets",                        [AdminTicketsUserList::class, "index"], []);
    $routes->get("support/ticket",                              [AdminTicketDetail::class, "index"], []);
    $routes->post("support/create_comment",                     [AdminCreateComment::class, "index"], []);
    $routes->post("support/change_status",                      [AdminChangeTicketStatus::class, "index"], []);
    // Transactions
    $routes->get("transactions/list",                           [AdminTransactions::class, "index"], []);
    $routes->get("transactions/subscribe",                      [AdminSubscribe::class, "index"], []);
    $routes->get("transactions/user_subscribe",                 [AdminUserSubscribes::class, "index"], []);
    // Users
    $routes->get("users/list",                                  [AdminUsersList::class, "index"], []);
    $routes->get("users/detail",                                [AdminUserDetail::class, "index"], []);
    $routes->post("users/update",                               [AdminUpdateUser::class, "index"], []);
    // Payment providers
    $routes->get("providers/list",                              [AdminProvidersList::class, "index"], []);
    $routes->post("providers/update",                           [AdminUpdateProvider::class, "index"], []);
    // Plans
    $routes->get("plans/list",                                  [AdminPlansList::class, "index"], []);
    $routes->post("plans/create",                               [AdminCreatePlan::class, "index"], []);
    $routes->post("plans/update",                               [AdminUpdatePlan::class, "index"], []);
    $routes->post("plans/remove",                               [AdminRemovePlan::class, "index"], []);
    // Settings
    $routes->get("settings/website",                            [AdminSiteSettings::class, "index"], []);
    $routes->post("settings/update_website",                    [AdminUpdateSiteSettings::class, "index"], []);
    $routes->post("settings/upload_logo",                       [AdminUploadLogo::class, "index"], []);
    $routes->get("settings/api",                                [AdminExternalApiSettings::class, "index"], []);
    $routes->post("settings/update_api",                        [AdminUpdateApiSettings::class, "index"], []);
    $routes->get("settings/onesignal",                          [AdminOneSignalSettings::class, "index"], []);
    $routes->post("settings/update_onesignal",                  [AdminUpdateOneSignal::class, "index"], []);
    $routes->post("settings/upload_fcm",                        [AdminUploadFcm::class, "index"], []);
    $routes->get("settings/email",                              [AdminEmailSettings::class, "index"], []);
    $routes->post("settings/update_email",                      [AdminUpdateEmailSettings::class, "index"], []);
    $routes->get("settings/license",                            [AdminLicenseSettings::class, "index"], []);
    $routes->post("settings/license_activation",                [AdminActivateLicense::class, "index"], []);
    $routes->get("settings/zarinpal",                           [AdminZarinPalSettings::class, "index"], []);
    $routes->post("settings/zarinpal",                          [AdminUpdateZarinPalSettings::class, "index"], []);
    $routes->get("settings/enamad",                             [AdminEnamadSettings::class, "index"], []);
    $routes->post("settings/enamad",                            [AdminUpdateEnamadSettings::class, "index"], []);
    $routes->get("settings/landing",                            [AdminLandingSettings::class, "index"], []);
    $routes->post("settings/landing",                           [AdminUpdateLandingSettings::class, "index"], []);
    // Dashboard
    $routes->get("dashboard/total",                             [AdminTotalStat::class, "index"], []);
    $routes->get("dashboard/chart",                             [AdminChartStat::class, "index"], []);
    // Templates Management
    $routes->get("templates/list",                              [AdminTemplatesList::class, "index"], []);
    $routes->get("templates/detail",                            [AdminTemplateDetail::class, "index"], []);
    $routes->post("templates/create",                           [AdminCreateTemplate::class, "index"], []);
    $routes->post("templates/update",                           [AdminUpdateTemplate::class, "index"], []);
    $routes->post("templates/delete",                           [AdminDeleteTemplate::class, "index"], []);
    $routes->post("templates/update_schema",                    [AdminUpdateFormSchema::class, "index"], []);
    $routes->post("templates/upload_thumbnail",                 [AdminUploadThumbnail::class, "index"], []);
    $routes->get("templates/categories",                        [AdminCategoriesList::class, "index"], []);
    // Languages & Translations Management
    $routes->get("languages/list",                              [AdminLanguagesList::class, "index"], []);
    $routes->post("languages/update",                           [AdminUpdateLanguage::class, "index"], []);
    $routes->get("translations/list",                           [AdminTranslationsList::class, "index"], []);
    $routes->post("translations/update",                        [AdminUpdateTranslation::class, "index"], []);
    $routes->post("translations/bulk_update",                   [AdminBulkUpdateTranslations::class, "index"], []);
});