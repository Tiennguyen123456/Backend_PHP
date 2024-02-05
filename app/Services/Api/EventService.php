<?php
namespace App\Services\Api;

use App\Repositories\Event\EventRepository;
use App\Services\BaseService;

class EventService extends BaseService
{
    public function __construct()
    {
        $this->repo = new EventRepository();
    }

    public function store()
    {
        $attrs = [
            'name'              => $this->attributes['name'],
            'start_time'         => $this->attributes['start_time'],
            'end_time'          => $this->attributes['end_time'],
            'is_default'        => $this->attributes['is_default'] ?? false,
            'description'       => $this->attributes['description'] ?? null,
            'location'          => $this->attributes['location'] ?? null,
            'note'              => $this->attributes['note'] ?? null,
            'contact_name'      => $this->attributes['contact_name'] ?? null,
            'contact_email'     => $this->attributes['contact_email'] ?? null,
            'contact_phone'     => $this->attributes['contact_phone'] ?? null,
            'status'            => $this->attributes['status'] ?? null,
        ];

        if (!isset($this->attributes['id'])) {
            $model = $this->init();

            $attrMores = [
                'company_id'            => $this->attributes['company_id'],
                'code'                  => $this->attributes['code'],
                'created_by'            => auth()->user()->id,
                'updated_by'            => auth()->user()->id,
                'main_field_templates'  => $model->buildDefaultMainFieldTemplate(),
            ];
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
                'status'        => $this->attributes['status']
            ];
        }

        return $this->storeAs($attrs, $attrMores);
    }

    public function assignCompany()
    {
        $id = $this->attributes['id'];
        $event = $this->find($id);

        if ($event) {
            $companyId = $this->attributes['company_id'];

            return $this->storeAs([
                'company_id'    => $companyId
            ], [
                'id'            => $id
            ]);
        }

        return null;
    }

    public function getFieldTemplate($id)
    {
        $event = $this->find($id);

        if ($event) {
            return [
                'id'            => $event->id,
                'template'      => $event->getFieldInputTemplate(),
                'main_fields'   => !empty($event->main_field_templates) ? $event->main_field_templates : $event->buildDefaultMainFieldTemplate(),
                'custom_fields' => $event->custom_field_templates
            ];
        }

        return [];
    }

    public function updateFieldTemplate()
    {
        $id = $this->attributes['event_id'];
        $event = $this->find($id);

        if ($event) {
            $mainFieldTemplates = $event->main_field_templates;

            if (!empty($this->attributes['data']['main_fields'])) {
                $requestMainFieldTemplates = $this->attributes['data']['main_fields'];

                foreach ($requestMainFieldTemplates as $field => $requestMainFieldTemplate) {
                    if (isset($mainFieldTemplates[$field])) {
                        $mainFieldTemplates[$field]['desc'] = $requestMainFieldTemplate['desc'];
                        $mainFieldTemplates[$field]['attributes'] = $requestMainFieldTemplate['attributes'];
                    }
                }

                $event->update([
                    'main_field_templates' => $mainFieldTemplates
                ]);
            }

            $customFieldTemplates = $event->custom_field_templates;

            if (!empty($this->attributes['data']['custom_fields'])) {
                $requestCustomFieldTemplates = $this->attributes['data']['custom_fields'];

                foreach ($requestCustomFieldTemplates as $field => $requestCustomFieldTemplate) {
                    if (isset($customFieldTemplates[$field])) {
                        $customFieldTemplates[$field] = $requestCustomFieldTemplate;
                    }
                }

                $event->update([
                    'custom_field_templates' => $customFieldTemplates
                ]);
            }

            return [
                'id'            => $event->id,
                'template'      => $event->getFieldTemplate(),
                'main_fields'   => $event->main_field_templates,
                'custom_fields' => $event->custom_field_templates
            ];
        }

        return [];
    }
}
