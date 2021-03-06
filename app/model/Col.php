<?php

namespace app\model;

class Col extends Base
{
    public function searchTitleAttr($query, $value, $data)
    {
        $value && $query->where('title', 'like', '%' . $value . '%');
    }
    public function searchDataIndexAttr($query, $value, $data)
    {
        $value && $query->where('data_index', 'like', '%' . $value . '%');
    }

    // 关联模型定义名
    public function getRelationNameAttr($value, $data)
    {
        return strrpos($data['data_index'], '.') ? substr($data['data_index'], 0, strrpos($data['data_index'], '.')) : '';
    }
    public function getProSchemaAttr($value, $data)
    {
        $schema = [
            'title' => $data['title'],
            'dataIndex' => strpos($data['data_index'], '.') ? explode('.', $data['data_index']) : $data['data_index'],
        ];
        !empty($data['tip']) && $schema['tooltip'] = $data['tip'];
        !empty($data['value_type']) && $schema['valueType'] = $data['value_type'];
        !empty($data['value_enum_dict_key']) && $schema['valueEnum'] = system_dict($data['value_enum_dict_key']);
        $data['col_size'] > 1 && $schema['colSize'] = $data['col_size'];
        $data['filters'] && $schema['filters'] = true;
        $data['ellipsis'] && $schema['ellipsis'] = true;
        $data['copyable'] && $schema['copyable'] = true;
        $data['hide_in_form'] && $schema['hideInForm'] = true;
        $data['hide_in_table'] && $schema['hideInTable'] = true;
        $data['hide_in_search'] && $schema['hideInSearch'] = true;
        $data['hide_in_descriptions'] && $schema['hideInDescriptions'] = true;

        return $schema;
    }
    public function getFormilySchemaAttr($value, $data)
    {
        $mapType = [
            'dateRange' => 'string[]',
            'dateTimeRange' => 'string[]',
            'timeRange' => 'string[]',
        ];
        $mapComponent = [
            'text' => 'Input',
            'select' => 'Select',
            'switch' => 'Switch',
            'digit' => 'NumberPicker',
            'money' => 'NumberPicker',
            'password' => 'Password',
            'treeSelect' => 'TreeSelect',
            'cascader' => 'Cascader',
            'textarea' => 'Input.TextArea',
            'code' => 'Input.TextArea',
            'jsonCode' => 'Input.TextArea',
            'radio' => 'Radio.Group',
            'checkbox' => 'Checkbox.Group',
            'rate' => 'Rate',
            'percent' => 'Slider',
            'progress' => 'Slider',
            'avatar' => 'Upload',
            'image' => 'Upload',
            //'color' => '',
            'date' => 'DatePicker',
            'dateTime' => 'DatePicker',
            'dateWeek' => 'DatePicker',
            'dateMonth' => 'DatePicker',
            'dateQuarter' => 'DatePicker',
            'dateYear' => 'DatePicker',
            'dateRange' => 'DatePicker.RangePicker',
            'dateTimeRange' => 'DatePicker.RangePicker',
            'time' => 'TimePicker',
            'timeRange' => 'TimePicker.RangePicker',
            //'second' => '',
            //'fromNow' => '',
            //'customRichText' => '',
        ];

        $schema = [
            'name' => $data['data_index'],
            'type' => $mapType[$data['value_type']] ?? 'string',
            'title' => $data['title'],
            'x-decorator' => 'FormItem',
            'x-component' => $mapComponent[$data['value_type']] ?? 'Input',
        ];
        !empty($data['required']) && $schema['required'] = true;
        $data['default_value'] != '' && $schema['default'] = is_numeric($data['default_value']) ? $data['default_value'] + 0 : $data['default_value'];
        !empty($data['value_enum_dict_key']) && $schema['enum'] = array_map(
            fn($key, $value) => ['value' => $key, 'label' => $value],
            array_keys(system_dict($data['value_enum_dict_key'])),
            array_values(system_dict($data['value_enum_dict_key']))
        );
        if ($data['value_type'] == 'avatar') {
            $schema['x-component-props'] = [
                'accept' => 'image/*',
                'listType' => 'picture-card',
                'multiple' => false,
                'maxCount' => 1,
            ];
        }
        if ($data['value_type'] == 'image') {
            $schema['x-component-props'] = [
                'accept' => 'image/*',
                'listType' => 'picture-card',
                'multiple' => true,
            ];
        }
        return $schema;
    }

    public function btable()
    {
        return $this->belongsTo(Table::class, 'table_code');
    }
}
