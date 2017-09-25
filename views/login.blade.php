@extends('app')

@section('title', t('ldap.login.title'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{t('ldap.login.heading')}}
                    </div>
                    <div class="panel-body">
                        @include('_errors')
                        <form class="form-horizontal" role="form" method="POST" action="{{url('ldap/login')}}">
                            <input name="_token" value="{{csrf_token()}}" type="hidden"/>
                            <div class="form-group{{error('username') ? ' has-error' : ''}}">
                                <label for="username" class="col-md-4 control-label">
                                    {{t('models.user.name')}}
                                </label>
                                <div class="col-md-8">
                                    <input id="username" name="username" value="{{old('username')}}" type="text" class="form-control" required="required" autofocus="autofocus"/>
                                    @if (error('username'))
                                        <span class="help-block">
                                            <strong>{{error('username')}}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{error('password') ? ' has-error' : ''}}">
                                <label for="password" class="col-md-4 control-label">
                                    {{t('models.user.password')}}
                                </label>
                                <div class="col-md-8">
                                    <input id="password" name="password" type="password" class="form-control" required="required"/>
                                    @if (error('password'))
                                        <span class="help-block">
                                            <strong>{{error('password')}}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-btn fa-sign-in"></i> {{t('ldap.login.submit')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
