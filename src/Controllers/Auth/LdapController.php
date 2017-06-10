<?php

namespace Pletfix\Ldap\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Core\Services\DI;
use Core\Services\Contracts\Response;

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
        // /** @var \Pletfix\Ldap\Services\Ldap $ldap */
        //$ldap = DI::getInstance()->get('ldap');
        $ldap = ldap();
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

        // Load the User entity from the database or create a new Model if not exist.
        $user = User::whereIs('principal', $attributes['userprincipalname'])->first();
        if ($user === null && !empty($attributes['mail'])) {
            $user = User::whereIs('email', $attributes['mail'])->first();
//            if ($user !== null) {
//                $user->principal = $attributes['userprincipalname'];
//                $user->save();
//            }
        }
        if ($user === null) {
            $user = new User;
            $user->principal = $attributes['userprincipalname'];
            $user->name      = $attributes['displayname'];
            $user->email     = $attributes['mail'];
            $user->role      = $attributes['role'];
            $user->save();
        }

        // Log the user into the application.
        auth()->setPrincipal($user->id, $user->name, $user->role); // todo kann auth() nicht auch vom Model entkoppelt werden?

        return redirect($this->redirectTo);
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
