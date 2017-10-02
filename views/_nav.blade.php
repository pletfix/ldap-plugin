@if(!auth()->isLoggedIn())
    <li {!! is_active('ldap/login') ? 'class="active"' : '' !!}>
        <a href="{{url('ldap/login')}}"><i class="fa fa-windows" aria-hidden="true"></i> {{t('ldap.nav.login')}}</a>
    </li>
@else
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            {{auth()->name()}}
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li {!! is_active('ldap/logout') ? 'class="active"' : '' !!}>
                <a href="{{url('ldap/logout')}}" onclick="event.preventDefault(); $(this).next().submit();">
                    <i class="fa fa-sign-out" aria-hidden="true"></i> {{t('ldap.nav.logout')}}
                </a>
                <form action="{{url('ldap/logout')}}" method="POST" style="display:none">
                    <input name="_token" value="{{csrf_token()}}" type="hidden"/>
                </form>
            </li>
        </ul>
    </li>
@endif
