<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			"id" => $this->id,
			"type" => "Invoice",
			"attributes" => [
				"number" => $this->number,
				"customer_name" => $this->customer_name,
				"amount_due" => $this->amount_due,
				"currency" => $this->currency,
				"created_at" => $this->created,
				"view_pdf_link" => $this->hosted_invoice_url,
				"download_pdf_link" => $this->invoice_pdf
			]
		];
	}
}


