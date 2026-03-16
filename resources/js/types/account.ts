export interface IAccount {
    id: string;
    id_int?: number;
    idHash: string;
    name: string;
    type: 'cash' | 'bank' | 'ewallet' | 'credit_card' | 'investment';
    balance: number;
    currency: string;
    color: string | null;
    icon: string | null;
    createdAt?: string;
    updatedAt?: string;
}

export interface IAccountPayload {
    name: string;
    type: 'cash' | 'bank' | 'ewallet' | 'credit_card' | 'investment';
    balance: number;
    currency: string;
    color?: string | null;
    icon?: string | null;
}
