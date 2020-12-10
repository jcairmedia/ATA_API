<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    use HasApiTokens;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'last_name1',
        'last_name2',
        'url_image',
        'confirmation_code',
        'phone',
        'email_verified_at',
        'facebook_user_id',
        'state',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function findFacebookUserForPassport($token)
    {
        // Your logic here using Socialite to push user data from Facebook generated token.

        // $providerUser = Socialite::driver('facebook')->userFromToken($token);
        try {
            $url = 'https://graph.facebook.com/v9.0/me?';
            $data = [
                'access_token' => $token,
                'fields' => 'name, first_name, email, last_name',
                'locale' => 'en_US',
                'method' => 'get',
                'transport' => 'cors',
            ];
            $query = http_build_query($data);
            \Log::error('query string: '.$query);
            $url .= $query;
            \Log::error('url: '.$url);
            $headers = [
                    'Content-type: application/json',
                    'Accept: application/json',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                ];
            \Log::error('Entro con la : '.$url);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //declared at the top of the doc

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);

            curl_close($ch);

            \Log::error(print_r($response, 1));
            $obj = json_decode($response, true);
            if (!isset($obj['email'])) {
                return null;
            }
            $user = self::where(['email' => $obj['email']])->first();

            if (is_null($user)) {
                $user = new self([
                    'email' => $obj['email'],
                    'name' => $obj['first_name'],
                    'last_name1' => $obj['last_name'],
                    'facebook_user_id' => $obj['id'],
                    'email_verified_at' => date('Y-m-d H:i:s'),
                ]);
                $user->save();
            }

            return $user;
        } catch (\Exception $ex) {
            \Log::error(print_r($ex->getMessage(), 1));
        }

        return null;
    }
}
