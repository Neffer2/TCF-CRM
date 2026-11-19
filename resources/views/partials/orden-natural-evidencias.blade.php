                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evidencias as $evidencia)
                                <tr>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $evidencia->fecha_evidencia }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">
                                            <a href="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" target="_blank">
                                                <img src="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" height="40">
                                            </a>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $evidencia->observacion_evidencia }}
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
