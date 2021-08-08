<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\HttpController;

SimpleRouter::get('/', [AppController::class, 'home'])->name('app.home');
SimpleRouter::get('/list/all/user/{user}/by/state/{state}', [AppController::class, 'list'])->name('app.list.state');
SimpleRouter::post('/auth/sign/in', [AuthController::class, 'signIn'])->name('auth.sigin');
SimpleRouter::get('/auth/sign/out', [AuthController::class, 'signOut'])->name('auth.signout');

SimpleRouter::group(['prefix' => 'ticket'], function() {
    SimpleRouter::get('/state/{state}', [TicketController::class, 'show'])->name('ticket.all.state');
    SimpleRouter::get('/by/id/{id}', [TicketController::class, 'show'])->name('ticket.show');
    SimpleRouter::get('/create/user/{user}', [TicketController::class, 'viewStore'])->name('ticket.store.view');
    SimpleRouter::form('/create/user/{user}/new', [TicketController::class, 'store'])->name('ticket.store');
});

SimpleRouter::group(['prefix' => 'request'], function () {
    SimpleRouter::post('/type/category', [HttpController::class, 'category'])->name('request.category');
    SimpleRouter::post('/type/fields', [HttpController::class,'fields'])->name('request.category');
    SimpleRouter::post('/type/entity', [HttpController::class,'entity'])->name('request.entity');
});
