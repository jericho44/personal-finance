import { IAccount } from './account';
import { ICategory } from './category';

export interface ITransaction {
    id_hash: string;
    account_id: number;
    target_account_id?: number | null;
    category_id?: number | null;
    type: 'income' | 'expense' | 'transfer';
    amount: number;
    date: string;
    notes?: string | null;
    account?: IAccount;
    targetAccount?: IAccount | null;
    category?: ICategory | null;
    created_at?: string;
    updated_at?: string;
}

export interface ITransactionPayload {
    account_id: number;
    target_account_id?: number | null;
    category_id?: number | null;
    type: 'income' | 'expense' | 'transfer';
    amount: number;
    date: string;
    notes?: string | null;
}

export interface ITransactionFilters {
    type?: 'all' | 'income' | 'expense' | 'transfer';
    account_id?: number | null;
    category_id?: number | null;
    start_date?: string | null;
    end_date?: string | null;
}
