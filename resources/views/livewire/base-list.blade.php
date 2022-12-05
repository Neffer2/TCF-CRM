<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    @foreach ($list as $item)
        <tr>
            <td class="text-sm font-weight-normal">{{ $item->fecha }}</td>
            <td class="text-sm font-weight-normal">{{ $item->nom_cliente }}</td>
            <td class="text-sm font-weight-normal">{{ $item->nom_proyecto }}</td>
            <td class="text-sm font-weight-normal">{{ $item->cod_cc }}</td>
            <td class="text-sm font-weight-normal">{{ number_format($item->valor_proyecto) }}</td>
            <td class="text-sm font-weight-normal">{{ $item->id_estado }}</td>
            <td class="text-sm font-weight-normal">{{ $item->fecha_inicio }}</td>
            <td class="text-sm font-weight-normal">{{ $item->dura_mes }}</td>
        </tr>
    @endforeach
</div>
