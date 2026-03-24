<?php

namespace App\Http\Controllers\Api\Docs;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="abc-123"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="username", type="string", example="johndoe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", nullable=true),
 *     @OA\Property(property="role", type="string", example="admin"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Account",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="acc-123"),
 *     @OA\Property(property="id_int", type="integer", example=1),
 *     @OA\Property(property="idHash", type="string", example="acc-123"),
 *     @OA\Property(property="name", type="string", example="BCA Main"),
 *     @OA\Property(property="type", type="string", example="bank"),
 *     @OA\Property(property="balance", type="number", format="float", example=5000000),
 *     @OA\Property(property="currency", type="string", example="IDR"),
 *     @OA\Property(property="color", type="string", example="#007bff"),
 *     @OA\Property(property="icon", type="string", example="bank"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="cat-123"),
 *     @OA\Property(property="id_int", type="integer", example=1),
 *     @OA\Property(property="idHash", type="string", example="cat-123"),
 *     @OA\Property(property="name", type="string", example="Food & Beverage"),
 *     @OA\Property(property="type", type="string", enum={"income", "expense"}),
 *     @OA\Property(property="color", type="string", example="#ff5733"),
 *     @OA\Property(property="icon", type="string", example="utensils"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="trx-123"),
 *     @OA\Property(property="id_int", type="integer", example=1),
 *     @OA\Property(property="idHash", type="string", example="trx-123"),
 *     @OA\Property(property="accountId", type="string", example="acc-123"),
 *     @OA\Property(property="targetAccountId", type="string", nullable=true, example="acc-456"),
 *     @OA\Property(property="categoryId", type="string", nullable=true, example="cat-123"),
 *     @OA\Property(property="type", type="string", enum={"income", "expense", "transfer"}),
 *     @OA\Property(property="amount", type="number", format="float", example=50000),
 *     @OA\Property(property="date", type="string", format="date", example="2024-03-24"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Lunch at Padang restaurant"),
 *     @OA\Property(property="account", ref="#/components/schemas/Account"),
 *     @OA\Property(property="targetAccount", ref="#/components/schemas/Account", nullable=true),
 *     @OA\Property(property="category", ref="#/components/schemas/Category", nullable=true),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Budget",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="bud-123"),
 *     @OA\Property(property="idHash", type="string", example="bud-123"),
 *     @OA\Property(property="categoryId", type="string", example="cat-123"),
 *     @OA\Property(property="amount", type="number", format="float", example=2000000),
 *     @OA\Property(property="startDate", type="string", format="date", example="2024-03-01"),
 *     @OA\Property(property="endDate", type="string", format="date", example="2024-03-31"),
 *     @OA\Property(property="isActive", type="boolean", example=true),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Bill",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="bil-123"),
 *     @OA\Property(property="name", type="string", example="Internet Subscription"),
 *     @OA\Property(property="amount", type="number", format="float", example=350000),
 *     @OA\Property(property="dueDate", type="string", format="date", example="2024-03-25"),
 *     @OA\Property(property="frequency", type="string", enum={"once", "monthly", "yearly"}),
 *     @OA\Property(property="isPaid", type="boolean", example=false),
 *     @OA\Property(property="categoryId", type="string", example="cat-123"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Goal",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="goa-123"),
 *     @OA\Property(property="name", type="string", example="Buy New Laptop"),
 *     @OA\Property(property="targetAmount", type="number", format="float", example=15000000),
 *     @OA\Property(property="currentAmount", type="number", format="float", example=5000000),
 *     @OA\Property(property="deadline", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="color", type="string", example="#28a745"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="isCompleted", type="boolean", example=false),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 */
class Schemas {}
