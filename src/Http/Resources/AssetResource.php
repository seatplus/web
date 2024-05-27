<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Web\Http\Resources\Universe\TypeResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'item_id' => $this->item_id,
            'owner_id' => $this->assetable_id,
            'quantity' => $this->quantity,
            'type_id' => $this->type_id,
            'type' => TypeResource::make($this->whenLoaded('type')),
            'volume' => $this->whenLoaded('type', fn () => $this->type->volume * $this->quantity),
            'name' => $this->name,
            'location_id' => $this->location_id,
            'location_flag' => $this->location_flag,
            'is_singleton' => $this->is_singleton,
            'is_blueprint_copy' => $this->is_blueprint_copy,
            'content' => $this::collection($this->whenLoaded('content')),
            'url' => route('character.item', [
                'character_id' => $this->assetable_id,
                'item_id' => $this->item_id
            ]),
        ];
    }
}
