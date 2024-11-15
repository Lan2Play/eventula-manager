<?php

namespace App;

use DB;
use Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Contracts\Encryption\DecryptException;

class ApiKey extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'api_keys';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    /**
     * Set Paypal Username
     * @param String $username
     * @return Boolean
     */
    public static function setPayPalUsername($username)
    {
        $key = self::where('key', 'paypal_username')->first();
        $key->value = $username;
        if (!$key->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Paypal Password
     * @param String $password
     * @return Boolean
     */
    public static function setPayPalPassword($password)
    {
        $key = self::where('key', 'paypal_password')->first();
        $key->value = $password;
        if (!$key->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Paypal Signature
     * @param String $signature
     * @return Boolean
     */
    public static function setPayPalSignature($signature)
    {
        $key = self::where('key', 'paypal_signature')->first();
        $key->value = $signature;
        if (!$key->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Stripe Public Key
     * @param String $publicKey
     * @return Boolean
     */
    public static function setStripePublicKey($publicKey)
    {
        $key = self::where('key', 'stripe_public_key')->first();
        $key->value = $publicKey;
        if (!$key->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Stripe Secret Key
     * @param String $secretKey
     * @return Boolean
     */
    public static function setStripeSecretKey($secretKey)
    {
        $key = self::where('key', 'stripe_secret_key')->first();
        $key->value = $secretKey;
        if (!$key->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Challonge Api Key
     * @param String $apiKey
     * @return Boolean
     */
    public static function setChallongeApiKey($apiKey)
    {
        $key = self::where('key', 'challonge_api_key')->first();
        $key->value = $apiKey;
        if (!$key->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Steam Api Key
     * @param String $apiKey
     * @return Boolean
     */
    public static function setSteamApiKey($apiKey)
    {
        $key = self::where('key', 'steam_api_key')->first();
        $key->value = $apiKey;
        if (!$key->save()) {
            return false;
        }
        return true;
    }

    public function getValueAttribute($value)
    {
        if ($value == null) {
            return null;
        }
        try {
            $decrypted = decrypt($value);
        } catch (DecryptException $e) {
            return null;
        }
        return $decrypted;
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = encrypt($value);
    }
}
