@extends('layout.app')
@section('title') Редактирование ИП @endsection

@section('content')

    <div class="ui styled fluid accordion">
        <div class="title">
            <h3>Информация о преподавателе</h3>
        </div>
        <div class="content">
            <table class="ui definition table">
                <tbody>
                <tr>
                    <td>Учебный год</td>
                    <td>{{ $file[0]['educationYear'] }}</td>
                </tr>
                <tr>
                    <td>Институт</td>
                    <td>{{ $file[0]['institute'] }}</td>
                </tr>
                <tr>
                    <td>Кафедра</td>
                    <td>{{ $file[0]['faculty'] }}</td>
                </tr>
                <tr>
                    <td>ФИО</td>
                    <td>{{ $file[0]['initials'] }}</td>
                </tr>
                <tr>
                    <td>Должность</td>
                    <td>{{ $file[0]['teacherPost'] }}</td>
                </tr>
                <tr>
                    <td>Вид ставки</td>
                    <td>
                        {{ $file[0]['rateType'] }}
                    </td>
                </tr>
                <tr>
                    <td>Значение ставки</td>
                    <td>
                        {{ $file[0]['rateValue'] }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ui styled fluid accordion">
        <div class="title">
            <h3>Список дисциплин за 1-й семестр</h3>
        </div>
        <div class="content">
            <table class="ui table">
                <colgroup>
                    <col width="5%">
                </colgroup>
                <thead>
                <tr>
                    <th>№</th>
                    <th>Наименование дисциплины</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($file[1]['subjects'] as $subject)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $subject }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="ui styled fluid accordion">
        <div class="title">
            <h3>Список дисциплин за 2-й семестр</h3>
        </div>
        <div class="content">
            <table class="ui table">
                <colgroup>
                    <col width="5%">
                </colgroup>
                <thead>
                <tr>
                    <th>№</th>
                    <th>Наименование дисциплины</th>
                </tr>
                </thead>
                <tbody>
                @empty($file[2])
                    <tr>
                        <td colspan="2">
                            <i class="massive frown icon"></i>
                        </td>
                    </tr>
                @else
                    @foreach ($file[2]['subjects'] as $subject)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subject }}</td>
                        </tr>
                    @endforeach
                @endempty
                </tbody>
            </table>
        </div>
    </div>

    <div class="ui styled fluid accordion">
        <div class="title">
            <h3>Учебно-методическая работа</h3>
        </div>
        <div class="content">
            <table class="ui table">
                <thead>
                <tr>
                    <th rowspan="2">№</th>
                    <th rowspan="2">Наименование и вид работ</th>
                    <th colspan="2">Трудоёмкость (час)</th>
                    <th rowspan="2">Форма завершения работ</th>
                    <th colspan="2">Срок выполнения (даты)</th>
                </tr>
                <tr>
                    <th>Планируемая</th>
                    <th>Фактическая</th>
                    <th>Планируемая</th>
                    <th>Фактическая</th>
                </tr>
                </thead>
                <tbody>
                @foreach($file[3]['work'] as $work)
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td>
                            {{ $work['caption'] }}
                        </td>
                        <td>
                            {{ $work['plan'] }}
                        </td>
                        <td>
                            {{ $work['real'] }}
                        </td>
                        <td>
                            {{ $work['finish'] }}
                        </td>
                        <td>
                            {{ $work['finishDatePlan'] }}
                        </td>
                        <td>
                            {{ $work['finishDateReal'] }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="ui styled fluid accordion">
        <div class="title">
            <h3>Научно-исследовательская работа</h3>
        </div>
        <div class="content">
            <table class="ui table">
                <colgroup>
                    <col width="5%">
                    <col width="45%">
                </colgroup>
                <thead>
                    <tr>
                        <th rowspan="2">№</th>
                        <th rowspan="2">Наименование и вид работ</th>
                        <th colspan="2">Трудоёмкость (час)</th>
                        <th colspan="2">Срок выполнения (даты)</th>
                    </tr>
                    <tr>
                        <th>Планируемая</th>
                        <th>Фактическая</th>
                        <th>Планируемая</th>
                        <th>Фактическая</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($file[4]['work_1'] as $work)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $work['caption'] }}
                            </td>
                            <td>
                                {{ $work['plan'] }}
                            </td>
                            <td>
                                {{ $work['real'] }}
                            </td>
                            <td>
                                {{ $work['finishDatePlan'] }}
                            </td>
                            <td>
                                {{ $work['finishDateReal'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="ui styled fluid accordion">
        <div class="title">
            <h3>Организационно-методическая и воспитательная работа</h3>
        </div>
        <div class="content">
            <table class="ui table">
                <colgroup>
                    <col width="5%">
                    <col width="45%">
                </colgroup>
                <thead>
                    <tr>
                        <th rowspan="2">№</th>
                        <th rowspan="2">Наименование и вид работ</th>
                        <th colspan="2">Трудоёмкость (час)</th>
                        <th colspan="2">Срок выполнения (даты)</th>
                    </tr>
                    <tr>
                        <th>Планируемая</th>
                        <th>Фактическая</th>
                        <th>Планируемая</th>
                        <th>Фактическая</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($file[4]['work_2'] as $work)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $work['caption'] }}
                            </td>
                            <td>
                                {{ $work['plan'] }}
                            </td>
                            <td>
                                {{ $work['real'] }}
                            </td>
                            <td>
                                {{ $work['finishDatePlan'] }}
                            </td>
                            <td>
                                {{ $work['finishDateReal'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <fieldset class="ui segment">
        <legend><h3>Общая годовая нагрузка</h3></legend>
        <table class="ui table">
            <col width="50%">
            <col width="25%">
            <col width="25%">
            <thead>
            <tr>
                <th>Виды работ</th>
                <th>Планируемая</th>
                <th>Фактическая</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Учебная работа</td>
                <td>
                    {{ $file[4]['workSum1'] }}
                </td>
                <td id="workSum1Real"></td>
            </tr>
            <tr>
                <td>Учебно-методическая работа</td>
                <td>
                    {{ $file[4]['workSum2'] }}
                </td>
                <td id="workSum2Real"></td>
            </tr>
            <tr>
                <td>Научно-исследовательская работа</td>
                <td>
                    {{ $file[4]['workSum3'] }}
                </td>
                <td id="workSum3Real"></td>
            </tr>
            <tr>
                <td>Организационно-методическая и воспитательная работа</td>
                <td>
                    {{ $file[4]['workSum4'] }}
                </td>
                <td id="workSum4Real"></td>
            </tr>
            <tr id="workSumRow">
                <td>Итого</td>
                <td>
                    {{ $file[4]['sum'] }}
                </td>
                <td id="workSumReal"></td>
            </tr>
            </tbody>
        </table>
    </fieldset>

@endsection