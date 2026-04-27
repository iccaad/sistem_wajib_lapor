<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the participant's user_id to exclude from NIK uniqueness check
        $participant = $this->route('participant');
        $userId = $participant ? $participant->user_id : null;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'digits:16', 'unique:users,nik,' . $userId],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'violation_type' => ['required', 'string', 'max:255'],
            'case_notes' => ['nullable', 'string'],
            'supervision_start' => ['required', 'date'],
            'supervision_end' => ['required', 'date', 'after:supervision_start'],
            'quota_type' => ['required', 'in:weekly,monthly'],
            'quota_amount' => ['required', 'integer', 'min:1', 'max:30'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar di sistem.',
            'violation_type.required' => 'Jenis pelanggaran wajib diisi.',
            'supervision_start.required' => 'Tanggal mulai wajib diisi.',
            'supervision_end.required' => 'Tanggal selesai wajib diisi.',
            'supervision_end.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'quota_type.required' => 'Tipe kuota wajib dipilih.',
            'quota_type.in' => 'Tipe kuota harus weekly atau monthly.',
            'quota_amount.required' => 'Jumlah kuota wajib diisi.',
            'quota_amount.min' => 'Jumlah kuota minimal 1.',
            'quota_amount.max' => 'Jumlah kuota maksimal 30.',
            'status.in' => 'Status harus active atau inactive.',
        ];
    }
}
