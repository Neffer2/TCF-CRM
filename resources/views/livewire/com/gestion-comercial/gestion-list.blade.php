<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Datos personales</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Datos corporativos</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Celular</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Siguiente estado</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Detalles</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $dato)
                <tr>
                    <td>
                        <div class="d-flex px-2 py-1">
                            <div>
                                <img src="https://demos.creative-tim.com/soft-ui-design-system-pro/assets/img/team-2.jpg" class="avatar avatar-sm me-3">
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-xs">{{ $dato->nombre }} {{ $dato->apellido }}</h6>
                                <p class="text-xs text-secondary mb-0">{{ $dato->correo }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $dato->cargo }}</p>
                        <p class="text-xs text-secondary mb-0">{{ $dato->empresa }}</p>
                    </td>
                    <td>
                        <p class="text-xs text-secondary mt-3">{{ $dato->celular }}</p>
                    </td>
                    <td>
                        <select name="" id="" class="form-control">
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="align-middle d-flex justify-content-center">
                        <button class="btn bg-gradient-warning">
                            ⏩
                        </button>
                    </td>
                    <td class="align-middle">
                        <button class="btn bg-gradient-primary">Ver mas</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
  </div>