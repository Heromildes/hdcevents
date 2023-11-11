<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {

        $search = request('search');

        if ($search) {
            $events = Event::where([
                ['title', 'like', '%'.$search.'%'],
            ])->get();
        } else {
            $events = Event::all();
        }



        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $event = new Event;

        $event->title = $request->title;
        $event->description = $request->description;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->items = $request->items;
        $event->date = $request->date;

        // Image Upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            // Pegar imagem
            $requestImage = $request->image;
            // Extenção da imagem
            $extension = $requestImage->extension();

            // Criptografar o nome da imagem para todos terem nomes diferentes e não dar conflito
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now") . "." . $extension);

            // Mover para a pasta public/img/events
            $requestImage->move(public_path('/img/events'), $imageName);

            // Dado que vai salvar no banco de dados
            $event->image = $imageName;
        }

        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);

        return view("events/show", ['event' => $event]);
    }
}
