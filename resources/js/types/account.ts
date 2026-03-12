export interface IAccount {
    id_hash: string;
    name: string;
    type: 'cash' | 'bank' | 'ewallet' | 'credit_card' | 'investment';
    balance: number;
    currency: string;
    color: string | null;
    icon: string | null;
    created_at?: string;
    updated_at?: string;
}

export interface IAccountPayload {
    name: string;
    type: 'cash' | 'bank' | 'ewallet' | 'credit_card' | 'investment';
    balance: number;
    currency: string;
    color?: string | null;
    icon?: string | null;
}
