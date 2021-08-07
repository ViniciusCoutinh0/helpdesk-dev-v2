<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\HttpController;

SimpleRouter::get('/', [AppController::class, 'home'])->name('app.home');
SimpleRouter::post('/auth/sign/in', [AuthController::class, 'signIn'])->name('auth.sigin');
SimpleRouter::get('/auth/sign/out', [AuthController::class, 'signOut'])->name('auth.signout');

SimpleRouter::get('/ticket/state/{state}', [TicketController::class, 'show'])->name('ticket.all.state');
SimpleRouter::get('/ticket/by/id/{id}', [TicketController::class, 'show'])->name('ticket.show');
SimpleRouter::get('/ticket/create/user/{user}', [TicketController::class, 'viewStore'])->name('ticket.store.view');
SimpleRouter::form('/ticket/create/user/{user}/new', [TicketController::class, 'store'])->name('ticket.store');

SimpleRouter::post('/request/type/category', [HttpController::class, 'category'])->name('request.category');
SimpleRouter::post('/request/type/fields', [HttpController::class,'fields'])->name('request.category');
SimpleRouter::post('/request/type/entity', [HttpController::class,'entity'])->name('request.entity');
