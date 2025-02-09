<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 *
 * @property int $id
 * @property int $company_id
 * @property bool $is_default
 * @property string $code
 * @property string $name
 * @property string $description
 * @property string $logo_path
 * @property string $location
 * @property bool $encrypt_file_link
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property array $main_field_template
 * @property array $custom_field_template
 * @property array $languages
 * @property string $contact_name
 * @property string $contact_email
 * @property string $contact_phone
 * @property string $note
 * @property string $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Company $company
 * @property User|null $user
 * @property Collection|Checkin[] $checkins
 * @property Collection|Client[] $clients
 * @property Collection|EventAsset[] $event_assets
 * @property Collection|EventSetting[] $event_settings
 * @property Collection|ExportLog[] $export_logs
 * @property Collection|LanguageDefine[] $language_defines
 * @property Collection|Organizer[] $organizers
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Event extends BaseModel
{
	protected $table = 'events';

    /* CONST */

    const MAIN_FIELD_EVENT_CODE         = 'EVENT_CODE';
    const MAIN_FIELD_EVENT_NAME         = 'EVENT_NAME';
    const MAIN_FIELD_EVENT_START_TIME   = 'EVENT_START_TIME';
    const MAIN_FIELD_EVENT_END_TIME     = 'EVENT_END_TIME';
    const MAIN_FIELD_EVENT_LOCATION     = 'EVENT_LOCATION';
    const MAIN_FIELD_EVENT_DESCRIPTION  = 'EVENT_DESCRIPTION';
    const MAIN_FIELD_CLIENT_QRCODE      = 'CLIENT_QRCODE';
    const MAIN_FIELD_CLIENT_FULLNAME    = 'CLIENT_FULLNAME';
    const MAIN_FIELD_CLIENT_EMAIL       = 'CLIENT_EMAIL';
    const MAIN_FIELD_CLIENT_PHONE       = 'CLIENT_PHONE';
    const MAIN_FIELD_CLIENT_ADDRESS       = 'CLIENT_ADDRESS';

    const MAIN_FIELDS = [
        self::MAIN_FIELD_EVENT_CODE         => 'Event Code',
        self::MAIN_FIELD_EVENT_NAME         => 'Event Name',
        self::MAIN_FIELD_EVENT_START_TIME   => 'Start Time',
        self::MAIN_FIELD_EVENT_END_TIME     => 'End Time',
        self::MAIN_FIELD_EVENT_LOCATION     => 'Location',
        self::MAIN_FIELD_EVENT_DESCRIPTION  => 'Description',
        self::MAIN_FIELD_CLIENT_QRCODE      => 'QR code',
        self::MAIN_FIELD_CLIENT_FULLNAME    => 'Client Name',
        self::MAIN_FIELD_CLIENT_EMAIL       => 'Client Email',
        self::MAIN_FIELD_CLIENT_PHONE       => 'Client Phone number',
        self::MAIN_FIELD_CLIENT_ADDRESS     => 'Client address',
    ];

	protected $casts = [
		'company_id'                => 'int',
		'start_time'                => 'datetime:Y-m-d H:i:s',
		'end_time'                  => 'datetime:Y-m-d H:i:s',
		'created_by'                => 'int',
		'updated_by'                => 'int',
        'updated_at'                => 'datetime:Y-m-d H:i:s',
		'created_at'                => 'datetime:Y-m-d H:i:s',
	];

	protected $fillable = [
		'company_id',
		'code',
		'name',
        'email_content',
        'cards_content',
		'description',
		'location',
		'start_time',
		'end_time',
		'status',
		'created_by',
		'updated_by',
        'updated_at',
	];

    /* RELATIONSHIP */

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function checkins()
	{
		return $this->hasMany(Checkin::class);
	}

	public function clients()
	{
		return $this->hasMany(Client::class);
	}

	public function event_assets()
	{
		return $this->hasMany(EventAsset::class);
	}

	public function custom_fields()
	{
		return $this->hasMany(EventCustomField::class);
	}

	public function event_settings()
	{
		return $this->hasMany(EventSetting::class);
	}

	public function export_logs()
	{
		return $this->hasMany(ExportLog::class);
	}

	public function language_defines()
	{
		return $this->hasMany(LanguageDefine::class);
	}

	public function organizers()
	{
		return $this->hasMany(Organizer::class);
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}

    /* CONST FUNCTIONS */

    static public function getMainFields()
    {
        return self::MAIN_FIELDS;
    }

    public function getFont($fontKey)
    {
        return self::getFonts()[$fontKey];
    }

    public static function getFonts()
    {
        return [
            'ROBOTO' => [
                'name'      => 'Roboto',
                'path'      => 'roboto.ttf',
                'link'      => 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Roboto:ital,wght@0,100;1,100&display=swap',
                'styles'    => [
                    'default'   => 'roboto.ttf',
                    'bold'      => 'roboto-bold.ttf',
                    'italic'    => 'roboto-italic.ttf',
                ]
            ],
            'ARIAL' => [
                'name'      => 'Arial',
                'path'      => 'arial.ttf',
                'link'      => '',
                'styles'    => [
                    'default'   => 'arial.ttf',
                    'bold'      => 'arial-bold.ttf',
                    'italic'    => 'arial-italic.ttf',
                ]
            ],
            'TIME_NEW_ROMAIN' => [
                'name'      => 'Times New Roman',
                'path'      => 'times-new-roman.ttf',
                'link'      => '',
                'styles'    => [
                    'default'   => 'times-new-roman.ttf',
                    'bold'      => 'times-new-roman-bold.ttf',
                    'italic'    => 'times-new-roman-italic.ttf',
                ]
            ],
        ];
    }

    public static function fonts()
    {
        $fonts = [];
        $defaultFonts = self::getFonts();

        foreach ($defaultFonts as $key => $font) {
            $fonts[$key] = $font['name'];
        }

        return $fonts;
    }

    /* FUNCTIONS */

    public static function getAttributeDetailTemplate()
    {
        return [
            "show"      => [
                "type"      => "checkbox",
                "default"   => false,
            ],
            "bold"      => [
                "type"      => "checkbox",
                "default"   => false,
            ],
            "italic"    => [
                "type"      => "checkbox",
                "default"   => false,
            ],
            "font_size" => [
                "type"      => "number",
                "default"   => 15,
            ],
            "font"      => [
                "type"      => "select",
                "options"   => self::fonts(),
                "default"   => "ARIAL",
            ],
            "color"     => [
                "type"      => "color",
                "default"   => "#000000",
            ],
            "v_align"   => [
                "type"      => "select",
                "options"   => [
                    "TOP"       => "Top",
                    "MIDDLE"    => "Middle",
                    "BOTTOM"    => "Bottom",
                ],
                "default"   => "TOP",
            ],
            "h_align"   => [
                "type"      => "select",
                "options"   => [
                    "LEFT"      => "Left",
                    "CENTER"    => "Center",
                    "RIGHT"     => "Right",
                ],
                "default" => "LEFT",
            ],
            "pos_x"     => [
                "type"      => "number",
                "default"   => 0,
            ],
            "pos_y"     => [
                "type"      => "number",
                "default"   => 0,
            ],
        ];
    }

    public function getFieldInputTemplate()
    {
        $template = [
            "field"         => [
                "type"      => "text",
            ],
            "desc"          => [
                "type"      => "text"
            ],
            "order"         => [
                "type"      => "hidden"
            ],
            "is_main"       => [
                "type"      => "hidden",
                "default"   => true
            ],
            "attributes"    => [
                "desktop"   => [],
                "mobile"    => [],
                "tablet"    => [],
            ],
        ];

        foreach ($template['attributes'] as $key => $detail) {
            $template['attributes'][$key] = $this->getAttributeDetailTemplate();
        }

        return $template;
    }

    // public static function buildDefaultMainFieldTemplate()
    // {

    //     foreach (self::getAttributeDetailTemplate() as $attr => $config) {
    //         $defaultDetailAttributes[$attr] = $config['default'];
    //     }

    //     foreach (self::getMainFields() as $field => $desc) {
    //         $mainFieldTemplate[$field] = [
    //             "field"         => $field,
    //             "desc"          => $desc,
    //             "is_main"       => true,
    //             "attributes"    => [
    //                 "desktop"   => $defaultDetailAttributes,
    //                 "mobile"    => $defaultDetailAttributes,
    //                 "tablet"    => $defaultDetailAttributes,
    //             ],
    //         ];
    //     }

    //     return $mainFieldTemplate;
    // }

    /* public function processFields($fieldTemplates)
    {
        $result = [];

        foreach ($fieldTemplates as $key => $template) {
            $result[$template['field']] = $template;
        }

        return $result;
    } */
}
