<?php

namespace App\Filament\Admin\Resources\PatrolResource\Pages;

use App\Filament\Admin\Resources\PatrolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditPatrol extends EditRecord
{
    protected static string $resource = PatrolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ── Process checkpoint face_photo & signature → save to Patrol ─────
        if (isset($data['checkpoint_face_photo_b64']) && $data['checkpoint_face_photo_b64'] && str_starts_with($data['checkpoint_face_photo_b64'], 'data:')) {
            try {
                [$meta, $b64] = explode(',', $data['checkpoint_face_photo_b64'], 2);
                $ext  = str_contains($meta, 'jpeg') ? 'jpg' : 'png';
                $path = 'checkpoint-face-photos/' . uniqid('face_') . '.' . $ext;
                Storage::disk('public')->put($path, base64_decode($b64));
                $data['face_photo'] = $path;
                \Illuminate\Support\Facades\Log::info('Face photo updated to patrol', ['path' => $path]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Face photo save failed', ['error' => $e->getMessage()]);
            }
        }

        if (isset($data['checkpoint_signature']) && $data['checkpoint_signature'] && str_starts_with($data['checkpoint_signature'], 'data:')) {
            try {
                [, $b64] = explode(',', $data['checkpoint_signature'], 2);
                $path = 'checkpoint-signatures/' . uniqid('sig_') . '.png';
                Storage::disk('public')->put($path, base64_decode($b64));
                $data['signature'] = $path;
                \Illuminate\Support\Facades\Log::info('Signature updated to patrol', ['path' => $path]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Signature save failed', ['error' => $e->getMessage()]);
            }
        }

        return $data;
    }
}
