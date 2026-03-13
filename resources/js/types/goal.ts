export interface IGoal {
    id: string;
    idHash: string;
    name: string;
    targetAmount: number;
    currentAmount: number;
    deadline?: string | null;
    color?: string | null;
    notes?: string | null;
    isCompleted: boolean;
    createdAt?: string;
    updatedAt?: string;
}

export interface IGoalPayload {
    name: string;
    target_amount: number;
    current_amount: number;
    deadline?: string | null;
    color?: string | null;
    notes?: string | null;
    is_completed: boolean;
}

export interface IGoalFilters {
    is_completed?: boolean | string;
}
