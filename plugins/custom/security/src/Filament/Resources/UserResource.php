<?php

namespace Custom\Security\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource\Pages;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Security\Filament\Resources\UserResource as BaseUserResource;

class UserResource extends BaseUserResource
{

}
