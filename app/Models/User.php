<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable;

	protected $primaryKey = "id_user";

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = ["name", "email", "password", "email_verified_at", "role"];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var list<string>
	 */
	protected $hidden = ["password", "remember_token"];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			"email_verified_at" => "datetime",
			"password" => "hashed",
		];
	}

	/**
	 * Get the user associated with the Student
	 *
	 * @return HasOne
	 */
	public function student(): HasOne
	{
		return $this->hasOne(Student::class, "user_id", "id_user");
	}

	/**
	 * Get the user associated with the Teacher
	 *
	 * @return HasOne
	 */
	public function teacher(): HasOne
	{
		return $this->hasOne(Teacher::class, "user_id", "id_user");
	}

	/**
	 * Get the student associated with the Homeroom Teacher
	 *
	 * @return HasMany
	 */
	public function studentHomeroom(): HasMany
	{
		return $this->hasMany(Student::class, "homeroom_teacher_id", "id_user");
	}
}
