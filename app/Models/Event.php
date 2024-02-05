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

    const MAIN_FIELD_QRCODE         = 'qrcode';
    const MAIN_FIELD_NAME           = 'name';
    const MAIN_FIELD_EMAIL          = 'email';
    const MAIN_FIELD_PHONE          = 'phone';

    const MAIN_FIELDS = [
        self::MAIN_FIELD_QRCODE     => 'Qrcode Identifier',
        self::MAIN_FIELD_NAME       => 'Full Name',
        self::MAIN_FIELD_EMAIL      => 'Email',
        self::MAIN_FIELD_PHONE      => 'Phone number',
    ];

	protected $casts = [
		'company_id'                => 'int',
		'is_default'                => 'bool',
		'encrypt_file_link'         => 'bool',
		'start_time'                => 'date',
		'end_time'                  => 'date',
		'main_field_templates'      => 'json',
		'custom_field_templates'    => 'json',
		'languages'                 => 'json',
		'created_by'                => 'int',
		'updated_by'                => 'int'
	];

	protected $fillable = [
		'company_id',
		'is_default',
		'code',
		'name',
		'description',
		'logo_path',
		'location',
		'encrypt_file_link',
		'start_time',
		'end_time',
		'main_field_templates',
		'custom_field_templates',
		'languages',
		'contact_name',
		'contact_email',
		'contact_phone',
		'note',
		'status',
		'created_by',
		'updated_by'
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

    public static function buildDefaultMainFieldTemplate()
    {

        foreach (self::getAttributeDetailTemplate() as $attr => $config) {
            $defaultDetailAttributes[$attr] = $config['default'];
        }

        foreach (self::getMainFields() as $field => $desc) {
            $mainFieldTemplate[$field] = [
                "field"         => $field,
                "desc"          => $desc,
                "is_main"       => true,
                "attributes"    => [
                    "desktop"   => $defaultDetailAttributes,
                    "mobile"    => $defaultDetailAttributes,
                    "tablet"    => $defaultDetailAttributes,
                ],
            ];
        }

        return $mainFieldTemplate;
    }

    /* public function processFields($fieldTemplates)
    {
        $result = [];

        foreach ($fieldTemplates as $key => $template) {
            $result[$template['field']] = $template;
        }

        return $result;
    } */
}
