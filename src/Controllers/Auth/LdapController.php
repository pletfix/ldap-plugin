<?php

namespace Pletfix\Ldap\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Core\Services\Contracts\Response;
use Core\Services\DI;

/**
 * This controller handles authentication users through the Active Directory and redirecting them to your home screen.
 */
class LdapController extends Controller
{
    /**
     * Where to redirect users after login or logout.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Show the application's login form.
     *
     * @return Response
     */
    public function showForm()
    {
        return view('auth.ldap');
    }

    /**
     * Handle a login request to the application.
     *
     * @return Response
     */
    public function login()
    {
        $input = request()->input();

        // Authenticate the user through the Active Directory.
         /** @var \Pletfix\Ldap\Services\Ldap $ldap */
        $ldap = DI::getInstance()->get('ldap');
        if (!$ldap->authenticate($input['username'], $input['password'])) {
            unset($input['password']);
            return redirect('auth/ldap', [], [
//                'errors' => ['Benutzername oder Kennwort ist nicht korrekt.'], // Invalid credentials
                'errors' => [$ldap->getErrorMessage()],
                'input'  => $input,
            ]);
        }

        // Load the User attributes from the Active Directory.
        $attributes = array_merge([
            'displayname' => null,
            'mail'        => null,
            'role'        => null,
        ], $ldap->getUser($input['username']));

        $model = config('ldap.model.class');
        if ($model !== null) {
            $mapping = array_merge(['userprincipalname' => 'principal'], config('ldap.model.mapping', []));
            $keyField = $mapping['userprincipalname'];

            // Load the User entity from the database or create a new Model if not exist.
            $user = call_user_func([$model, 'where'], $keyField, $attributes['userprincipalname'])->first();
            //$user = User::where('principal', $attributes['userprincipalname'])->first();
            if ($user === null && isset($mapping['mail']) && !empty($attributes['mail'])) {
                $user = call_user_func([$model, 'where'], $mapping['mail'], $attributes['mail'])->first();
                //$user = User::where('email', $attributes['mail'])->first();
            }
            if ($user === null) {
                $user = new User;
                foreach ($mapping as $adAttr => $modelAttr) {
                    $user->setAttribute($modelAttr, $attributes[$adAttr]);
                }
                $user->save();
            }

            // Log the user into the application.
            auth()->setPrincipal($user->id, $user->name, $user->role);
        }
        else {
            auth()->setPrincipal(uniqid(), $attributes['displayname'], $attributes['role']);
        }

        $url = session('origin_url', url($this->redirectTo));

        return response()->redirect($url);
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function logout()
    {
        auth()->logout();

        return redirect($this->redirectTo);
    }
}
