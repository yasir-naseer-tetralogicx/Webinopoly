<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\WalletController;
use App\Mail\NewUser;
use App\Mail\NewWallet;
use App\Mail\TopShopifyProuctMail;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/check/roles';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        /*Assigning User Role of Non-Shopify-User */
        $user->assignRole('non-shopify-users');
        $wallet = new WalletController();
        $wallet->wallet_create($user->id);
        try{
            Mail::to($user->email)->send(new NewUser($user));
            Mail::to($user->email)->send(new NewWallet($user));
            Mail::to($user->email)->send(new TopShopifyProuctMail($user));
        }
        catch (\Exception $e){
        }

        // Sync To SendGrid WefullFill Members Contact List
        $contacts = [];
        array_push($contacts, [
            'email' => $user->email,
            'first_name' => $user->name,
        ]);
        $contacts_payload = [
            'list_ids' => ["33d743f3-a906-4512-83cd-001f7ba5ab33"],
            'contacts' => $contacts
        ];
        $payload = json_encode($contacts_payload);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendgrid.com/v3/marketing/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer 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",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

//        if ($err) {} else {}

        return  $user;
    }
}
