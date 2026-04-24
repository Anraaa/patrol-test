<?php

namespace App\Forms\Components;

use Filament\Forms\Components\FileUpload;

class PhotoCaptureField extends FileUpload
{
    protected string $view = 'filament.forms.components.photo-capture-field';

    public static function make(string $name): static
    {
        return parent::make($name)
            ->multiple()
            ->image()
            ->directory('patrol-photos')
            ->maxFiles(5)
            ->reorderable()
            ->downloadable()
            ->previewable();
    }
}

