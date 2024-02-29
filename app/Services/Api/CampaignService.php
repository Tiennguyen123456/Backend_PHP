<?php
namespace App\Services\Api;

use App\Services\BaseService;
use App\Repositories\Campaign\CampaignRepository;

class CampaignService extends BaseService
{
    public function __construct()
    {
        $this->repo = new CampaignRepository();
    }

    public function store()
    {
        $attrs = [
            'name'              => $this->attributes['name'],
            'run_time'          => $this->attributes['run_time'],
            'status'            => $this->attributes['status'],
            'mail_subject'      => $this->attributes['mail_subject'],
            'sender_email'      => $this->attributes['sender_email'] ?? null,
            'sender_name'       => $this->attributes['sender_name'] ?? null,
            'description'       => $this->attributes['description'] ?? null,
            'filter_client'     => serialize($this->attributes['filter_client']),
        ];

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'company_id'        => $this->attributes['company_id'],
                'event_id'          => $this->attributes['event_id'],
                'mail_content'      => $this->attributes['mail_content'],
                'created_by'        => auth()->user()->id,
                'updated_by'        => auth()->user()->id
            ];
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
            ];
        }

        return $this->storeAs($attrs, $attrMores);
    }

    public function updateMailContent()
    {
        $id = $this->attributes['id'];

        $model = $this->repo->find($id);

        if (!empty($model)) {
            $model->update($this->attributes);
            return $model;
        }
    }
}
