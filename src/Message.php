<?php

namespace Pharaonic\Laravel\Messages;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Pharaonic\Laravel\Files\HasFiles;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $mobile_code
 * @property string $mobile_number
 * @property string $subject
 * @property string $message
 * @property string $IP
 * @property boolean $has_read
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property object|null $sender
 *
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class Message extends Model
{
    use HasFiles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'mobile_code', 'mobile_number',
        'subject', 'message',
        'IP', 'has_read',
        'sender_type', 'sender_id'
    ];

    /**
     * Files Fields
     *
     * @var array
     */
    protected $filesAttributes  = ['attachment'];

    /**
     * Files Options
     *
     * @var array
     */
    protected $filesOptions     = [
        'attachment' => ['directory' => '/messages/attachments']
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * Check if messaging allowed by IP
     *
     * @param Request $request
     * @param integer $minutes
     * @return boolean
     */
    public static function messageable(Request $request, int $minutes = 10)
    {
        $model = self::where('IP', $request->ip())->orderBy('created_at', 'desc')->first();
        if (!$model) return true;

        return Carbon::now() >= $model->created_at->addMinutes($minutes);
    }

    /**
     * Send Message
     *
     * @param Request $request
     * @return boolean
     */
    public static function send(Request $request)
    {
        $user = $request->user();

        $msg = self::create([
            'name'              => $request->name ?? $user->name ?? null,
            'email'             => $request->email ?? $user->email ?? null,
            'mobile_code'       => $request->mobile_code ?? $user->mobile_code ?? null,
            'mobile_number'     => $request->mobile_number ?? $user->mobile_number ?? null,
            'subject'           => $request->subject ?? null,
            'message'           => $request->message,
            'IP'                => $request->ip(),
            'sender_type'       => $user ? get_class($user) : null,
            'sender_id'         => $user ? $user->{$user->getKeyName()} : null,
        ]);

        if ($request->hasFile('attachment'))
            $msg->attachment = $request->file('attachment');

        return $msg;
    }

    /**
     * Mark As Read
     *
     * @return boolean
     */
    public function markAsRead()
    {
        return $this->update(['has_read' => true]);
    }
}
