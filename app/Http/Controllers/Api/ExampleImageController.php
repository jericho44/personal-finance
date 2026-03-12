<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $image = $request->file('image');

        $paths = [];

        for ($i = 0; $i < 10; $i++) {
            $paths[] = $image->store('image');
            $paths[] = \App\Helpers\Image::store($image, 'image_compress');
        }

        // [
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b1180468639_34268110.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b1181704404_64703520.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b1182a4b597_36096537.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b1184026091_34637646.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b11855d8750_34047648.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b1186ac2ea2_70244862.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b1187dfed76_42992723.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b118937c662_25400287.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b118a725592_57473510.jpg",
        //     "image/6fJwX4Gll6THpIsgQA6fb6Q9j6tbRin5qvjC8P10.jpg",
        //     "image_compress/ic_681b118bac18b9_56004428.jpg"
        // ]
        return $paths;
    }
}
