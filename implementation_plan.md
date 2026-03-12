# Implementation Plan: Authentication UI

## Goal Description
The backend API already supports comprehensive Authentication, including Role-based Access Control (RBAC), 2FA, and Profile management ([routes/api.php](file:///c:/laragon/www/personal-finance/routes/api.php)). However, the Vue 3 frontend currently only has a Login View ([resources/js/views/Login.vue](file:///c:/laragon/www/personal-finance/resources/js/views/Login.vue)) and does not have the UI to register new users, edit their profile, or set up 2FA. The goal of this phase is to build the missing Vue components and integrate them with the API.

## User Review Required
> [!IMPORTANT]
> Since we are building the missing UI pages, I recommend the following setup for 2FA and Profiles:
> 1. A public `/register` page for new users.
> 2. A protected `/admin/profile` view for the user to edit details, change passwords, and view/setup their Google 2FA QR code.
> Let me know if you approve of this approach before we write the Vue components.

## Proposed Changes
### Vue Views & Components
#### [NEW] [Register.vue](file:///c:/laragon/www/personal-finance/resources/js/views/Register.vue)
A public page mapping to the `/api/auth/register` endpoint.

#### [NEW] [Profile.vue](file:///c:/laragon/www/personal-finance/resources/js/views/admin/Profile.vue)
A protected page under the dashboard mapping to `/api/auth/me`, `/api/auth/change-password`, and `/api/auth/qrcode-url-google2fa`.

### Vue Routing Updates
#### [MODIFY] [authorization.ts](file:///c:/laragon/www/personal-finance/resources/js/routes/authorization.ts)
Add the register route here.
#### [MODIFY] [index.ts](file:///c:/laragon/www/personal-finance/resources/js/routes/index.ts)
Add the profile route under the protected `/admin` children structure.

### Store & API Logic
#### [MODIFY] [authorization.ts (store)](file:///c:/laragon/www/personal-finance/resources/js/stores/authorization.ts)
Update the store to manage actions for registering and fetching/enabling 2FA from the API.

## Verification Plan
### Manual Verification
1. Run `npm run dev` and test the `/register` page.
2. Sign in as the new user and go to the Profile page to set up 2FA.
3. Test that the API successfully returns the 2FA QR Code url.
