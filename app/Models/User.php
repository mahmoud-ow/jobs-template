<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $guarded = [];
    protected $hidden = ['password'];

    public function registerMediaCollections() : void 
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile()
            ->registerMediaConversions(function(Media $media = null){
                
                $this->addMediaConversion('thumb')
                    ->format('webp')
                    ->width(200)
                    ->height(200);

                $this->addMediaConversion('thumb-sm')
                    ->format('webp')
                    ->width(50)
                    ->height(50);
                    
            });
           
    }

}