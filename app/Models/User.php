<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class User extends Authenticatable
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable, SoftDeletes;

	protected $primaryKey = "id_user";

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = ["name", "slug", "email", "password", "email_verified_at", "role"];

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
	 * Get the student's data from the Student model as a student
	 *
	 * @return HasOne
	 */
	public function student(): HasOne
	{
		return $this->hasOne(Student::class, "user_id", "id_user");
	}

	/**
	 * Get the teacher's data from the Teacher model as a teacher
	 *
	 * @return HasOne
	 */
	public function teacher(): HasOne
	{
		return $this->hasOne(Teacher::class, "user_id", "id_user");
	}

	/**
	 * Get all students assigned to this user as their homeroom teacher.
	 *
	 * @return HasMany
	 */
	public function homeroomStudents(): HasMany
	{
		return $this->hasMany(Student::class, "homeroom_teacher_id", "id_user");
	}

	/**
	 * Get all internship records associated with this user as a student.
	 *
	 * @return HasMany
	 */
	public function internships(): HasMany
	{
		return $this->hasMany(Internship::class, "student_id", "id_user");
	}

	/**
	 * Get all internship records where the user is the supervising teacher.
	 *
	 * @return HasMany
	 */
	public function supervisedInternships(): HasMany
	{
		return $this->hasMany(Internship::class, "teacher_id", "id_user");
	}

	/**
	 * Get all attendance records associated with this user as a student.
	 *
	 * @return HasMany
	 */
	public function attendances(): HasMany
	{
		return $this->hasMany(Attendance::class, "student_id", "id_user");
	}

	/**
	 * Get all journal records associated with this user as a student.
	 *
	 * @return HasMany
	 */
	public function journals(): HasMany
	{
		return $this->hasMany(Journal::class, "student_id", "id_user");
	}

	/**
	 * Get all attendance records associated with this user as a supervised teacher.
	 *
	 * @return HasMany
	 */
	public function studentAttendances(): HasMany
	{
		return $this->hasMany(Attendance::class, "teacher_id", "id_user");
	}

	/**
	 * Get all journal records associated with this user as a supervised teacher.
	 *
	 * @return HasMany
	 */
	public function studentJournals(): HasMany
	{
		return $this->hasMany(Journal::class, "teacher_id", "id_user");
	}

	/**
	 * Get instruments records associated with this user as a head of department.
	 *
	 * @return HasOne
	 */
	public function instrument(): HasOne
	{
		return $this->hasOne(Instrument::class, "teacher_id", "id_user");
	}
}
