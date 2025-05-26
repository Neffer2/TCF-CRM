<head>
    <style>
        body {
            font-size: 12px;
            color: black !important;
        }

        .table {
            margin-top: 1rem;
            color: black !important;
            width: 100%;
        }

        .table-list {
            margin: .5rem 3rem;
        }

        p {
            margin: 0;
            padding: 0 3rem;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }

        .title {
            text-align: center;
            padding: 0 3rem;
        }

        .bold {
            font-weight: bold;
        }

        .legal {
            font-size: 9px;
            color: black !important;
        }

        .sign {
            font-family: 'DejaVu Serif'; font-style: italic;
            font-size: 24px;
            font-style: italic
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="title">CONTRATO DE PRESTACION DE SERVICIOS.</h3> <br>
        <p>
            Entre los suscritos a saber, <b>BULL MARKETING SAS</b>, sociedad legalmente constituida e identificada con
            el NIT. 900.298.176-1 y con domicilio principal en la ciudad de Bogotá D.C., y representada por el Sr.
            LUIS ALEJANDRO RODRÍGUEZ MENDOZA identificado como aparece al pie de su firma quien en lo
            sucesivo se denominará <b>LA SOCIEDAD CONTRATANTE</b>, y por la otra el Sr. <b>{{ $contratoInfo['tercero']->nombre }} {{ $contratoInfo['tercero']->apellido }}</b>,
            persona mayor de edad e identificado con la cédula de ciudadanía No. {{ $contratoInfo['tercero']->cedula }} y quien, para los
            efectos del presente contrato, se identificará con su número de RUT. Nº {{ $contratoInfo['num_rut'] }}, y quien actuará
            en calidad de <b>EL CONTRATISTA</b>, en adelante “Las Partes” han decidido y acordar celebrar el presente
            <b>CONTRATO DE PRESTACION DE SERVICIOS</b>, que se regirá por las siguientes: <br>
        </p>
        <h3 class="title"> CLÁUSULAS: </h3>
        <p>
            <b>PRIMERA.- OBJETO DEL CONTRATO:</b> El objeto del contrato será la prestación por parte de EL
            CONTRATISTA de la actividad de <b> @foreach ($contratoInfo['items'] as $item) {{ $item->tipo_servicio }}, @endforeach</b>
            quien para el presente contrato es profesional en el procedimiento de extensión de pestañas semipermanentes, por esta razón EL
            CONTRATISTA asumirá la responsabilidad de todos las obligaciones emanadas del presente contrato,
            dada la índole profesional y especializada que ostenta, características del EL CONTRATISTA necesarias
            para poder cumplir con el objeto del presente contrato.
        </p>
        <br>
        <p>
            <b>SEGUNDA.- NATURALEZA JURÍDICA:</b> El presente contrato es de orden mercantil, y no conlleva para EL
            CONTRATISTA relaciones de subordinación o dependencia laboral, técnica o administrativa.
            Consecuentemente EL CONTRATISTA utilizará sus propios medios, conocimientos, técnicas y
            capacidades para prestar el servicio independiente contratado. El presente contrato tampoco genera
            relación laboral de ninguna naturaleza con Empleados o Dependientes contratados al servicio de EL
            CONTRATISTA ya que no se ampara en supuestos de subordinación jurídica ni dependencia jerárquica.
            Parágrafo Primero: Igualmente podrá LA SOCIEDAD CONTRATANTE señalar a EL CONTRATISTA
            variaciones en la programación, así como definir políticas y filosofías propias de la entidad
            CONTRATANTE a las cuales debe sujetarse EL CONTRATISTA en desarrollo de su gestión, sin que por
            ello signifique subordinación de ningún tipo. Parágrafo Segundo: EL CONTRATISTA se obliga a mantener
            indemne a LA SOCIEDAD CONTRATANTE ante cualquier reclamación que se pueda presentar en virtud
            del desarrollo del presente contrato, o como consecuencia del mismo. Así mismo, EL CONTRATISTA
            deberá cumplir en forma eficiente y oportuna su labor y las demás obligaciones que se generen de
            acuerdo con la naturaleza del servicio que presta y especialmente se obliga a dar cumplimiento a lo
            estipulado en el Art. 15 de la Ley 100/93, modificado por el Art. 3º. de la Ley 707/2003 y el Art. 1º.
            del D.R. 510/2003 y demás normas concordantes, en lo que se refiere a la obligatoriedad de su
            afiliación y/o la de sus empleados o trabajadores al Sistema General de Seguridad Social y Riesgos
            Laborales (Pensiones, Salud y ARL),cuando ello fuere legalmente obligatorio. Su incumplimiento dará
            lugar a la terminación del contrato suscrito entre las partes. Parágrafo Tercero: Así mismo, LA
            SOCIEDAD CONTRATISTA se obliga a pres
        </p>
        <br>
        <p>
            <b>TERCERA.- REQUISITOS PARA EL PAGO:</b> LA SOCIEDAD CONTRATANTE, realizara los pagos durante la
            vigencia de la relación profesional y por servicios, y dentro de los cinco (5) días siguientes a la
            presentación de la cuenta de cobro, pago que se podrá hacer mediante transferencia electrónica de
            fondos a la cuenta previamente indicada por EL CONTRATISTA, siempre y cuando se cumpla y alleguen
            los siguientes documentos:
        </p>
        <table class="table-list">
            <tr>
                <td><b>a)</b> Cuenta de cobro.</td>
            </tr>
            <tr>
                <td><b>b)</b> RUT.</td>
            </tr>
            <tr>
                <td>
                    <b>c)</b> Copia de la planilla de liquidación de aportes a la Seguridad Social Integral con soporte de pago,
                    realizado durante los primeros <br> cinco (5) días hábiles de cada mes; de no hacerlo en el tiempo
                    estipulado el contrato se dará por terminado. Esto en los términos <br> del Art.18 de la ley 1122 de
                    enero de 2007 y el Decreto 1273 de 2018.
                </td>
            </tr>
        </table>
        <p>
            <b>CUARTA.- LUGAR y VIGILANCIA DEL CONTRATO:</b> Para todos los efectos legales el lugar de ejecución
            del presente contrato será la ciudad de {{ $contratoInfo['ciudad'] }}, LA SOCIEDAD CONTRATANTE o su representante
            podrán supervisar la ejecución de las actividades que debe realizar EL CONTRATISTA durante la vigencia
            del presente Contrato. LA SOCIEDAD CONTRATANTE Y El CONTRATISTA conviene que este Contrato
            entrará en vigencia a partir del día {{ $contratoInfo['dia_str'] }} ({{ $contratoInfo['dia'] }}) de {{ $contratoInfo['mes'] }}
            del año {{ $contratoInfo['ano'] }}; No obstante, el presente contrato
            finalizará por voluntad de una de las partes, para lo cual se fija un aviso previo de cinco (05) días hábiles,
            para que la parte interesada en no continuar con el servicio le comunique a la otra por escrito, sin que
            el hecho de la terminación unilateral dé lugar a pago alguno de indemnización.
        </p>
        <br>
        <p>
            <b>QUINTA.- OBLIGACIONES DE LAS PARTES:</b> a) LA SOCIEDAD CONTRATANTE deberá facilitar lo necesario
            para la debida ejecución objeto del contrato y estará obligada a cumplir lo estipulado en las demás
            cláusulas y condiciones previstas en este documento, entre ellas el pago oportuno de los HONORARIOS
            a favor de EL CONTRATISTA; b) EL CONTRATISTA pondrá todo su empeño, cuidado, capacidad y
            profesionalismo para el desarrollo del objeto pactado en la cláusula PRIMERA y SEGUNDA de éste
            contrato y a fin de evitar la causación de perjuicios para sí o para LA SOCIEDAD CONTRATANTE.
        </p>
        <br>
        <p>
            <b>SEXTA.- AUDITORIA:</b> LA SOCIEDAD CONTRATANTE estará en libertad de realizar auditorías totales o
            parciales en cualquier tiempo, con el fin de revisar que EL CONTRATISTA este desarrollando el objeto del
            contrato de acuerdo a las condiciones establecidas. EL CONTRATISTA deberá permitir que se realicen
            dichas auditorias, además de atender en debida forma los requerimientos o ajustes que se puedan
            generar por dichas auditorias, siempre en procura de prestar un excelente servicio profesional.
        </p>
        <br>
        <p>
            <b>SÉPTIMA.- TERMINACIÓN ANTICIPADA:</b> Las partes acuerdan que podrán dar por terminado de común
            acuerdo el presente contrato antes de su vencimiento, mediante comunicación por escrita con un
            preaviso de cinco (05) días, previa presentación del paz y salvo, que deberá expedir LA SOCIEDAD
            CONTRATANTE, donde advierta que EL CONTRATISTA no tiene pendientes de ningún tipo ni bajo ningún
            concepto. Los Honorarios pactados se cancelarán a prorrata de acuerdo a las actividades y ejecutada
            por EL CONTRATISTA, y durante la vigencia del contrato.
        </p>
        <br>
        <p>
            <b>OCTAVA.- CAUSALES DE TERMINACIÓN.</b> El contrato terminará por las siguientes causas:
        </p>
        <table class="table-list">
            <tr>
                <td><b>a)</b> Vencimiento del término inicialmente pactado.</td>
            </tr>
            <tr>
                <td><b>b)</b> Por incumplimiento de las obligaciones pactadas en el contrato, para lo cual bastará
                    comunicación por escrito indicando <br> los motivos que conllevan a la terminación.</td>
            </tr>
            <tr>
                <td><b>c)</b> Por mutuo acuerdo entre las partes. En este caso, se deberá aplicar dentro de lo pactado la
                    terminación anticipada establecida <br>en la cláusula anterior del presente contrato.</td>
            </tr>
            <tr>
                <td><b>d)</b> Por decisión unilateral de una de las partes, para lo cual se deberá de cumplir con el preaviso
                    pactado en este contrato.</td>
            </tr>
            <tr>
                <td><b>e)</b> Por que el CONTRATISTA no cumpla con la obligación legal de estar afiliado a la seguridad social
                    como contratista <br> independiente en los términos Decreto 1273 de 2018 y la Ley 2381 de 2024.</td>
            </tr>
        </table>
        <p>
            <b>NOVENA.- CONFIDENCIALIDAD:</b> El CONTRATISTA queda obligado a guardar reserva absoluta sobre la
            información del negocio de LA SOCIEDAD CONTRATANTE a la cual tenga acceso, conozca, analice,
            cualifique y cuantifique con ocasión de la ejecución del presente contrato.
        </p>
        <br>
        <p>
            <b>DÉCIMA.- PROPIEDAD INTELECTUAL:</b> EL CONTRATISTA reconoce que la información, documentos,
            informes, estudios, diseños, programas, bases de datos y demás productos que se generen en virtud del
            presente contrato son de propiedad exclusiva de LA SOCIEDAD CONTRATANTE y no podrá hacer uso de
            ellos sin la autorización previa y escrita de LA SOCIEDAD CONTRATANTE.
        </p>
        <table class="table" style="padding: 0 3rem;">
            <tr>
                <td>
                    <p class="bold">LA SOCIEDAD CONTRATANTE:<br></p>
                    <p><b>DIRECCIÓN:</b> carrera 53 c #127 d 23. <br></p>
                    <p><b>TELÉFONO:</b> (+57) 318 3723773. <br></p>
                    <p><b>EMAIL:</b> info@bullmarketing.com.co <br></p>
                </td>
                <td>
                    <p class="bold">EL CONTRATISTA:<br></p>
                    <p><b>TELÉFONO:</b> (+57) {{ $contratoInfo['telefono'] }}. <br></p>
                    <p><b>EMAIL:</b> info@bullmarketing.com.co <br></p>
                </td>
            </tr>
        </table>
        <br>
        <p>
            Para constancia y aceptación de lo anterior, se firma el presente contrato en la ciudad de Bogotá D.C.,
            en dos ejemplares del mismo tenor, a los {{ $contratoInfo['dia_str'] }} ({{ $contratoInfo['dia'] }}) días del mes de {{ $contratoInfo['mes'] }} de {{ $contratoInfo['ano'] }} por las
            partes que en el intervienen.
        </p>
        <br>
        <table class="table">
            <tr>
                <td style="text-align: center;">
                    <p class="bold">LA SOCIEDAD CONTRATANTE. <br><br><br></p>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/signs/sign.png'))) }}" alt="Firma">
                    <p class="bold">__________________________</p>
                    <p class="bold">BULL MARKETING S.A.S.</p>
                    <p>Representante Legal</p>
                    <p>NIT. 900.298.176-1</p>
                </td>
                <td style="text-align: center;">
                    <p class="bold">EL CONTRATISTA. <br><br><br></p>
                    <p class="sign"> {{ $contratoInfo['tercero']->nombre }} {{ $contratoInfo['tercero']->apellido }}</p>
                    <p class="bold">__________________________</p>
                    <p class="bold">Nombre: {{ $contratoInfo['tercero']->nombre }} {{ $contratoInfo['tercero']->apellido }}</p>
                    <p>CEDULA. {{ $contratoInfo['num_rut'] }}</p>
                    <p><br></p>
                </td>
            </tr>
        </table>
        <br><br><br>
        <table class="table legal">
            <tr>
                <td>
                    <p>
                        Vo. Bo. <br>
                        Líder Legal – 13-05-2025
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
