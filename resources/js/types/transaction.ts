import { IAccount } from './account';
import { ICategory } from './category';

export interface ITransaction {
    id: string;
    idHash: string;
    accountId: number | string | null;
    targetAccountId?: number | string | null;
    categoryId?: number | string | null;
    type: 'income' | 'expense' | 'transfer';
    amount: number;
    date: string;
    notes?: string | null;
    account?: IAccount;
    targetAccount?: IAccount | null;
    category?: ICategory | null;
    createdAt?: string;
    updatedAt?: string;
}

export interface ITransactionPayload {
    account_id: number | string | null;
    target_account_id?: number | string | null;
    category_id?: number | string | null;
    type: 'income' | 'expense' | 'transfer';
    amount: number;
    date: string;
    notes?: string | null;
}

export interface ITransactionFilters {
    type?: 'all' | 'income' | 'expense' | 'transfer';
    account_id?: number | string | null;
    category_id?: number | string | null;
    start_date?: string | null;
    end_date?: string | null;
}
