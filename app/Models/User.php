<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'payment_status',
    'gcash_reference',
    'gcash_receipt_path',
    'payment_submitted_at',
    'payment_approved_at',
    'password_reset_code',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_APPROVED = 'approved';
    public const PAYMENT_REJECTED = 'rejected';
    public const PAYMENT_VALIDITY_DAYS = 30;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'payment_submitted_at' => 'datetime',
            'payment_approved_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function examAttempts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function hasApprovedPayment(): bool
    {
        return $this->payment_status === self::PAYMENT_APPROVED;
    }

    public function paymentExpiresAt(): ?Carbon
    {
        if (! $this->payment_approved_at) {
            return null;
        }

        return $this->payment_approved_at->copy()->addDays(self::PAYMENT_VALIDITY_DAYS);
    }

    public function requiresPaymentRenewal(): bool
    {
        return $this->isStudent()
            && $this->hasApprovedPayment()
            && $this->paymentExpiresAt()?->isPast();
    }

    public function effectivePaymentStatus(): string
    {
        if ($this->requiresPaymentRenewal()) {
            return 'expired';
        }

        return $this->payment_status ?? self::PAYMENT_PENDING;
    }

    public function loginBlockedReason(): ?string
    {
        if (! $this->isStudent()) {
            return null;
        }

        return match ($this->payment_status) {
            self::PAYMENT_PENDING => 'Your GCash payment is pending admin approval.',
            self::PAYMENT_REJECTED => 'Your GCash payment was rejected. Please contact the administrator and resubmit payment details.',
            default => null,
        };
    }
}
