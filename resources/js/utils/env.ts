export const env = (
    env: string,
    defaultValue: string | number | null = ''
): string | number | null => {
    const value = import.meta.env[env as keyof ImportMetaEnv];
    return value ?? defaultValue;
};
