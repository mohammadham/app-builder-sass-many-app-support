import { createWebHistory, createRouter } from "vue-router";
import Login from "@/views/auth/Login.vue";
import AuthLayout from "@/components/layouts/AuthLayout.vue";
import Registration from "@/views/auth/Registration.vue";
import Forgot from "@/views/auth/Forgot.vue";
import Reset from "@/views/auth/Reset.vue";
import PrivateLayout from "@/components/layouts/PrivateLayout.vue";
import Apps from "@/views/private/apps/Apps.vue";
import Tickets from "@/views/private/support/Tickets.vue";
import ProfileMain from "@/views/private/profile/ProfileMain.vue";
import MainAppSettings from "@/views/private/apps/editor/main_app_settings/MainAppSettings.vue";
import DesignAppSettings from "@/views/private/apps/editor/design_app_settings/DesignAppSettings.vue";
import PermissionsAppSettings from "@/views/private/apps/editor/permissions_app_settings/PermissionsAppSettings.vue";
import NavigationAppSettings from "@/views/private/apps/editor/navigation_app_settings/NavigationAppSettings.vue";
import LocalizationAppSettings from "@/views/private/apps/editor/localization_app_settings/LocalizationAppSettings.vue";
import AssetsAppSettings from "@/views/private/apps/editor/assets_app_settings/AssetsAppSettings.vue";
import BuildService from "@/views/private/apps/services/build_service/BuildService.vue";
import NewsletterService from "@/views/private/apps/services/newsletter_service/NewsletterService.vue";
import KeyService from "@/views/private/apps/services/key_service/KeyService.vue";
import SplashscreenAppSettings from "@/views/private/apps/editor/splashscreen_app_settings/SplashscreenAppSettings.vue";
import TemplateConfig from "@/views/private/apps/editor/template_config/TemplateConfig.vue";
import AdminLanguagesList from "@/views/admin/languages/AdminLanguagesList.vue";
import AdminTranslationsEditor from "@/views/admin/languages/AdminTranslationsEditor.vue";
import AdminTemplatesList from "@/views/admin/templates/AdminTemplatesList.vue";
import AdminTemplateEditor from "@/views/admin/templates/AdminTemplateEditor.vue";
import Subscribe from "@/views/private/profile/Subscribe.vue";
import Transactions from "@/views/private/profile/Transactions.vue";
import ArchiveTickets from "@/views/private/support/ArchiveTickets.vue";
import Ticket from "@/views/private/support/Ticket.vue";
import Dashboard from "@/views/admin/dashboard/Dashboard.vue";
import AdminLayout from "@/components/layouts/AdminLayout.vue";
import Customers from "@/views/admin/customers/Customers.vue";
import AdminApps from "@/views/admin/apps/AdminApps.vue";
import AdminTickets from "@/views/admin/support/AdminTickets.vue";
import AdminTransactions from "@/views/admin/transactions/AdminTransactions.vue";
import AdminPlans from "@/views/admin/plans/AdminPlans.vue";
import AdminProviders from "@/views/admin/providers/AdminProviders.vue";
import AdminSettings from "@/views/admin/settings/AdminSettings.vue";
import AdminMainAppSettings from "@/views/admin/apps/editor/main_app_settings/AdminMainAppSettings.vue";
import AdminDesignAppSettings from "@/views/admin/apps/editor/design_app_settings/AdminDesignAppSettings.vue";
import AdminPermissionsAppSettings
  from "@/views/admin/apps/editor/permissions_app_settings/AdminPermissionsAppSettings.vue";
import AdminNavigationAppSettings
  from "@/views/admin/apps/editor/navigation_app_settings/AdminNavigationAppSettings.vue";
import AdminLocalizationAppSettings
  from "@/views/admin/apps/editor/localization_app_settings/AdminLocalizationAppSettings.vue";
import AdminSplashscreenAppSettings
  from "@/views/admin/apps/editor/splashscreen_app_settings/AdminSplashscreenAppSettings.vue";
import AdminAssetsAppSettings from "@/views/admin/apps/editor/assets_app_settings/AdminAssetsAppSettings.vue";
import AdminBuildService from "@/views/admin/apps/services/build_service/AdminBuildService.vue";
import AdminKeyService from "@/views/admin/apps/services/key_service/AdminKeyService.vue";
import AdminNewsletterService from "@/views/admin/apps/services/newsletter_service/AdminNewsletterService.vue";
import AdminPendingTickets from "@/views/admin/support/AdminPendingTickets.vue";
import AdminArchiveTickets from "@/views/admin/support/AdminArchiveTickets.vue";
import AdminTicket from "@/views/admin/support/AdminTicket.vue";
import AdminSubscribe from "@/views/admin/transactions/AdminSubscribe.vue";
import CustomerProfile from "@/views/admin/customers/detail/CustomerProfile.vue";
import CustomerApps from "@/views/admin/customers/detail/CustomerApps.vue";
import CustomerTickets from "@/views/admin/customers/detail/CustomerTickets.vue";
import CustomerSubscribes from "@/views/admin/customers/detail/CustomerSubscribes.vue";
import AdminApiSettings from "@/views/admin/settings/AdminApiSettings.vue";
import AdminPushSettings from "@/views/admin/settings/AdminPushSettings.vue";
import AdminEmailSettings from "@/views/admin/settings/AdminEmailSettings.vue";
import AdminLicense from "@/views/admin/settings/AdminLicense.vue";
import AdminZarinPalSettings from "@/views/admin/settings/AdminZarinPalSettings.vue";
import AdminEnamadSettings from "@/views/admin/settings/AdminEnamadSettings.vue";
import AdminLandingSettings from "@/views/admin/settings/AdminLandingSettings.vue";
import TemplatesGallery from "@/views/public/TemplatesGallery.vue";

const routes = [
  {
    path: '/templates',
    name: 'TemplatesGallery',
    component: TemplatesGallery,
  },
  {
    path: "/",
    component: AuthLayout,
    children: [
      {
        path: '',
        name: 'Login',
        component: Login,
      },
      {
        path: '/auth/sign_up',
        name: 'Registration',
        component: Registration,
      },
      {
        path: '/auth/forgot',
        name: 'Forgot',
        component: Forgot,
      },
      {
        path: '/auth/reset',
        name: 'Reset',
        component: Reset,
      },
    ]
  },
  {
    path: '/private/',
    component: PrivateLayout,
    children: [
      {
        path: '/private/apps',
        name: 'Apps',
        component: Apps,
      },
      {
        path: '/private/apps/:uid/main',
        name: 'MainAppSettings',
        component: MainAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/design',
        name: 'DesignAppSettings',
        component: DesignAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/permissions',
        name: 'PermissionsAppSettings',
        component: PermissionsAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/navigation',
        name: 'NavigationAppSettings',
        component: NavigationAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/localization',
        name: 'LocalizationAppSettings',
        component: LocalizationAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/assets',
        name: 'AssetsAppSettings',
        component: AssetsAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/splashscreen',
        name: 'SplashscreenAppSettings',
        component: SplashscreenAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/template-config',
        name: 'TemplateConfig',
        component: TemplateConfig,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/build',
        name: 'BuildAppService',
        component: BuildService,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/push',
        name: 'NewsletterAppService',
        component: NewsletterService,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/private/apps/:uid/signing',
        name: 'KeyAppService',
        component: KeyService,
        meta: { sidebar: 'editor'}
      },
      // Tickets
      {
        path: '/private/support',
        name: 'Tickets',
        component: Tickets,
        meta: { sidebar: 'support'}
      },
      {
        path: '/private/support/archive',
        name: 'ArchiveTickets',
        component: ArchiveTickets,
        meta: { sidebar: 'support'}
      },
      {
        path: '/private/support/ticket/:ticket_uid',
        name: 'Ticket',
        component: Ticket,
        meta: { sidebar: 'support'}
      },
      // Profile
      {
        path: '/private/profile',
        name: 'Profile',
        component: ProfileMain,
        meta: { sidebar: 'profile'}
      },
      {
        path: '/private/profile/subscribe',
        name: 'Subscribe',
        component: Subscribe,
        meta: { sidebar: 'profile'}
      },
      {
        path: '/private/profile/transactions/:subscribe_uid',
        name: 'Transactions',
        component: Transactions,
        meta: { sidebar: 'profile'}
      },
    ]
  },
  {
    path: '/admin/',
    component: AdminLayout,
    children: [
      {
        path: '/admin/dashboard',
        name: 'Dashboard',
        component: Dashboard,
      },
      {
        path: '/admin/customers',
        name: 'Customers',
        component: Customers,
      },
      {
        path: '/admin/customers/:customer_id/profile',
        name: 'AdminCustomerProfile',
        component: CustomerProfile,
        meta: { sidebar: 'profile'}
      },
      {
        path: '/admin/customers/:customer_id/apps',
        name: 'AdminCustomerApps',
        component: CustomerApps,
        meta: { sidebar: 'profile'}
      },
      {
        path: '/admin/customers/:customer_id/tickets',
        name: 'AdminCustomerTickets',
        component: CustomerTickets,
        meta: { sidebar: 'profile'}
      },
      {
        path: '/admin/customers/:customer_id/subscribes',
        name: 'AdminCustomerSubscribes',
        component: CustomerSubscribes,
        meta: { sidebar: 'profile'}
      },
      {
        path: '/admin/apps',
        name: 'AdminApps',
        component: AdminApps,
      },
      {
        path: '/admin/apps/:uid/main',
        name: 'AdminMainAppSettings',
        component: AdminMainAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/design',
        name: 'AdminDesignAppSettings',
        component: AdminDesignAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/permissions',
        name: 'AdminPermissionsAppSettings',
        component: AdminPermissionsAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/navigation',
        name: 'AdminNavigationAppSettings',
        component: AdminNavigationAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/localization',
        name: 'AdminLocalizationAppSettings',
        component: AdminLocalizationAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/splashscreen',
        name: 'AdminSplashscreenAppSettings',
        component: AdminSplashscreenAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/assets',
        name: 'AdminAssetsAppSettings',
        component: AdminAssetsAppSettings,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/build',
        name: 'AdminBuildAppService',
        component: AdminBuildService,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/signing',
        name: 'AdminKeyAppService',
        component: AdminKeyService,
        meta: { sidebar: 'editor'}
      },
      {
        path: '/admin/apps/:uid/push',
        name: 'AdminNewsletterAppService',
        component: AdminNewsletterService,
        meta: { sidebar: 'editor'}
      },
      // ######### //
      {
        path: '/admin/support',
        name: 'AdminTickets',
        component: AdminTickets,
        meta: { sidebar: 'support'}
      },
      {
        path: '/admin/support/pending',
        name: 'AdminPendingTickets',
        component: AdminPendingTickets,
        meta: { sidebar: 'support'}
      },
      {
        path: '/admin/support/archive',
        name: 'AdminArchiveTickets',
        component: AdminArchiveTickets,
        meta: { sidebar: 'support'}
      },
      {
        path: '/admin/support/ticket/:ticket_uid',
        name: 'AdminTicket',
        component: AdminTicket,
        meta: { sidebar: 'support'}
      },
      {
        path: '/admin/transactions',
        name: 'AdminTransactions',
        component: AdminTransactions,
      },
      {
        path: '/admin/transactions/subscribe/:external_uid',
        name: 'AdminSubscribe',
        component: AdminSubscribe,
      },
      {
        path: '/admin/plans',
        name: 'AdminPlans',
        component: AdminPlans,
      },
      {
        path: '/admin/providers',
        name: 'AdminProviders',
        component: AdminProviders,
      },
      {
        path: '/admin/settings',
        name: 'AdminSettings',
        component: AdminSettings,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/settings/api',
        name: 'AdminApiSettings',
        component: AdminApiSettings,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/settings/push',
        name: 'AdminPushSettings',
        component: AdminPushSettings,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/settings/email',
        name: 'AdminEmailSettings',
        component: AdminEmailSettings,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/settings/license',
        name: 'AdminLicense',
        component: AdminLicense,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/settings/zarinpal',
        name: 'AdminZarinPalSettings',
        component: AdminZarinPalSettings,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/settings/enamad',
        name: 'AdminEnamadSettings',
        component: AdminEnamadSettings,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/settings/landing',
        name: 'AdminLandingSettings',
        component: AdminLandingSettings,
        meta: { sidebar: 'settings'}
      },
      {
        path: '/admin/languages',
        name: 'AdminLanguagesList',
        component: AdminLanguagesList,
      },
      {
        path: '/admin/translations',
        name: 'AdminTranslationsEditor',
        component: AdminTranslationsEditor,
      },
      {
        path: '/admin/templates',
        name: 'AdminTemplatesList',
        component: AdminTemplatesList,
      },
      {
        path: '/admin/templates/:uid',
        name: 'AdminTemplateEditor',
        component: AdminTemplateEditor,
      },
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
