import { IRole } from "./user";

export interface ILoginPayload {
    username: string,
    password: string,
}

export interface IRegisterPayload {
    name: string,
    username: string,
    password: string,
    password_confirmation: string,
}

export interface IUserProfile {
    authorized: boolean,
    id: string,
    name: string,
    role: IRole | null,
    telegram_id?: string | null
}

export interface IChangePasswordPayload {
    currentPassword: string,
    newPassword: string,
    newPasswordConfirmation: string,
}

export interface IForgotPasswordPayload {
    email: string,
}

export interface IResetPasswordPayload {
    email: string,
    token: string,
    password: string,
    password_confirmation: string,
}
