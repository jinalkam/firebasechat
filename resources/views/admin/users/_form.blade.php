<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>{{ $datatitle }} Appointment Template</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content add-new-reminder-box">
            <div class="">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-6s">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            {{ Form::label('title', 'Title') }}  
                            {{ Form::text('title', null, array('class' => 'form-control')) }}  
                            @if ($errors->has('title'))
                            <span class="help-block">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group{{ $errors->has('purpose') ? ' has-error' : '' }}">
                            {{ Form::label('purpose', 'Appointment Type') }}  
                            {{ Form::text('purpose', null, array('class' => 'form-control')) }}  
                            @if ($errors->has('purpose'))
                            <span class="help-block">
                                <strong>{{ $errors->first('purpose') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group{{ $errors->has('language') ? ' has-error' : '' }}">
                            {{ Form::label('language', 'Language') }}  
                            {{ Form::select('language',[""=>"Please select language"]+$language,null,['class'=>'form-control'])}}
                            @if ($errors->has('language'))
                            <span class="help-block">
                                <strong>{{ $errors->first('language') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>   

                </div>
                <div class="row">
                    <div class="col-md-12">

                        <div class="form-group note-editable-group{{ $errors->has('bodyemail') ? ' has-error' : '' }}">
                            {{ Form::label('bodyemail', 'Event Body') }}  
                            {{ Form::textarea('bodyemail', null, ['class' => 'form-control']) }}
                            @if ($errors->has('bodyemail'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bodyemail') }}</strong>
                            </span>
                            @endif
                        </div>


                    </div>
                </div>

                <div class="row add-new-checkbox">


                    <div class="col-md-4 col-sm-4 col-xs-6 addfields">
                        <div class="form-group{{ $errors->has('fields') ? ' has-error' : '' }}">
                            {{ Form::label('fields', 'Insert Field') }}  
                            {{ Form::select('fields',[""=>'Please select fields']+$fields,null,['class'=>'form-control'])}}
                            @if ($errors->has('fields'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fields') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div> 


                    <div class="col-md-6 col-sm-8 col-xs-6">
                        <div class="m-b-20"></div>
                        <?php
                        $checkeddiabled = ((isset($mastertemplates) && $mastertemplates->isreschedule ) == 'Y' ? 1 : 0); ?>
                        {{ Form::checkbox('isreschedule','Y',$checkeddiabled) }}
                        <span>Disable Cancel/Reschedle Options.</span>

                    </div> 


                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Participant Details</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <div class="row add-new-checkbox reminder-types-table">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 120px">Reminder Types</th>
                                    @foreach ($flagcolumn as $key=>$value)

                                    <th>
                                        <div class="i-checks">
                                            
                                                <?php if($key==0){
                                                  $checked= ((isset($mastertemplates) && $mastertemplates->flagsms =='Y')?1:0);
                                                }
                                                elseif($key==1) {
                                                   $checked=((isset($mastertemplates) && $mastertemplates->flagcall=='Y')?1:0);
                                                }
                                                elseif($key==2) {
                                                   $checked= ((isset($mastertemplates) && $mastertemplates->flagemail =='Y')?1:0);
                                                }?>
                                            <label> {{ Form::checkbox($value,$value,$checked,['class'=>'select-colcheckboxhead','data'=>'dataid'.$key]) }}<span> {{ $value}}</span></label>
                                        </div>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($slots as $key=>$value)
                                <tr>  
                                    @foreach ($column as $columns)
                                    <td>
                                        <div class="i-checks">
                                            <label>
                                                <?php
                                               
                                                $checkedsms = (isset($mastertemplates))?in_array($value['slotId'], $mastertemplates->smstimingid): '';
                                                $checkedcall = (isset($mastertemplates))? in_array($value['slotId'], $mastertemplates->calltimingid):'';
                                                $checkedemail = (isset($mastertemplates))? in_array($value['slotId'], $mastertemplates->emailtimingid):'';
                                                $checkedrow = ($checkedsms == 1 && $checkedcall == 1 && $checkedemail == 1)??'';
                                                
                                                ?>

                                                @if($columns==1)

                                                {{ Form::checkbox('smstimingid[]',$value['slotId'],$checkedsms,['class'=>'select-colcheckbox','data'=>'dataid0']) }}<span> {{$value['slots'] }}</span>

                                                @elseif($columns==2)
                                                {{ Form::checkbox('calltimingid[]',$value['slotId'],$checkedcall,['class'=>'select-colcheckbox','data'=>'dataid1']) }}<span> {{$value['slots'] }}</span>

                                                @elseif($columns==3)
                                                {{ Form::checkbox('emailtimingid[]',$value['slotId'],$checkedemail,['class'=>'select-colcheckbox','data'=>'dataid2']) }}<span> {{$value['slots'] }}</span>
                                                @else


                                                {{ Form::checkbox('timingid[]',$value['slotId'],$checkedrow,['class'=>'select-rowcheckbox']) }}

                                                @endif
                                            </label>
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>                         
            <div class="participant-details-box">
                <div class="row">                                        
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="submit-event-btn">

                            <a class="btn btn-danger btn-sm" href="{{ URL::route('mastertemplates.index') }}">Cancel</a>

                            {!! Form::submit('Save', ['class' => 'btn btn-primary import-appointment-btn btn-sm']) !!}


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
