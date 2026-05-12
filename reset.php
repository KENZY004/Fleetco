<?php $u = App\Models\User::where('email', 'kenzninnu409@gmail.com')->first(); $u->password = Hash::make('driver1@123'); $u->save(); echo 'OK';
