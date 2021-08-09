<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use Pecee\SimpleRouter\SimpleRouter;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\HttpController;

SimpleRouter::get('/', [AppController::class, 'home'])->name('app.home');
SimpleRouter::get('/list/all/user/{user}/by/state/{state}', [AppController::class, 'list'])->name('app.list.state');
SimpleRouter::post('/auth/sign/in', [AuthController::class, 'signIn'])->name('auth.sigin');
SimpleRouter::get('/auth/sign/out', [AuthController::class, 'signOut'])->name('auth.signout');

SimpleRouter::group(['prefix' => 'ticket'], function () {
    SimpleRouter::get('/state/{state}', [TicketController::class, 'show'])->name('ticket.all.state');
    SimpleRouter::get('/by/id/{id}', [TicketController::class, 'show'])->name('ticket.show');
    SimpleRouter::get('/create/user/{user}', [TicketController::class, 'viewStore'])->name('ticket.store.view');
    SimpleRouter::form('/create/user/{user}/new', [TicketController::class, 'store'])->name('ticket.store');
    SimpleRouter::post('/commit/by/{id}', [TicketController::class, 'commitStore'])->name('commit.store');
});

SimpleRouter::group(['prefix' => 'account'], function () {
    SimpleRouter::get('/user/{user}', [AccountController::class, 'viewAccount'])->name('account.view');
    SimpleRouter::get('/user/{user}/update/password', [AccountController::class, 'viewPassword'])->name('account.password');
    SimpleRouter::post('/user/{user}/update/password/true', [AccountController::class, 'storePassword'])->name('account.store.password');
});

SimpleRouter::group(['prefix' => 'admin'], function () {
    SimpleRouter::get('/list/all/users', [])->name('admin.list.all');
    SimpleRouter::get('/list/all/sections', [])->name('admin.list.all');

    SimpleRouter::get('/create/new/users', [])->name('admin.list.all');
    SimpleRouter::get('/create/new/section', [])->name('admin.list.all');

    SimpleRouter::get('/update/user/{user}', [])->name('admin.list.all');
    SimpleRouter::get('/update/section/{section}', [])->name('admin.list.all');

    SimpleRouter::get('/{user}/report', [AdminController::class, 'viewCreateReport'])->name('admin.view.report');
    SimpleRouter::post('{user}/report/create/between/date', [AdminController::class, 'createReport'])->name('admin.create.report');
});

SimpleRouter::group(['prefix' => 'request'], function () {
    SimpleRouter::post('/type/category', [HttpController::class, 'category'])->name('request.category');
    SimpleRouter::post('/type/fields', [HttpController::class,'fields'])->name('request.category');
    SimpleRouter::post('/type/entity', [HttpController::class,'entity'])->name('request.entity');
});
