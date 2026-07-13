<?php
class GalleryController extends Controller
{
    public function index(): void
    {
        $this->render('gallery/index', [
            'pageTitle' => 'Gallery',
            'albums'    => Database::all("SELECT * FROM gallery_albums ORDER BY sort_order"),
            'images'    => Database::all("SELECT gi.*, ga.title AS album_title FROM gallery_items gi
                                          LEFT JOIN gallery_albums ga ON ga.id = gi.album_id
                                          WHERE gi.type='image' ORDER BY gi.id DESC"),
            'videos'    => Database::all("SELECT * FROM gallery_items WHERE type='video' ORDER BY id DESC"),
        ]);
    }
}
