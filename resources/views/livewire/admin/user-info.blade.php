<div>
    <div class="card">
        <div class="card-body pt-2">  
            <span class="text-gradient text-primary text-uppercase text-xs font-weight-bold my-2">
                @if ($userInfo)
                    {{ $userInfo->user_rol->description }}
                @endif
            </span> 
            <a href="javascript:;" class="card-title h5 d-block text-darker">
                @if ($userInfo)
                    {{ $userInfo->name }}
                @endif
            </a>
            <p class="card-description mb-4">
                <ul style="list-style-type: square;">
                    <li>
                        <div>
                            <i class="ni ni-email-83"></i>
                            @if ($userInfo)
                                {{ $userInfo->email }}
                            @endif
                        </div>
                    </li>
                    <li>
                        <div>
                            <i class="ni ni-tablet-button"></i>
                            @if ($userInfo)
                                {{ $userInfo->telefono }}                                
                            @endif
                        </div>
                    </li>
                </ul>
            </p>
            <div class="author align-items-center">
                @php
                    if ($userInfo){
                        $aux = str_replace('public/', '', $userInfo->avatar);    
                    }
                @endphp
                <img @if ($userInfo) src="{{ asset("storage/$aux") }}" @endif alt="Foto de perfil" class="avatar shadow">
                    <div class="name ps-3">
                        <span>
                            @if ($userInfo)
                                {{ $userInfo->name }}        
                            @endif
                        </span>
                    <div class="stats">
                        <small>
                            @if ($userInfo)
                                {{ $userInfo->created_at }}
                            @endif
                        </small>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
