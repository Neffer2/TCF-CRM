<head>
    <style>
        body {
            font-size: 12px;
            color: black !important;
        }

        .table {
            margin-top: 2rem;
            color: black !important;
            width: 100%;
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
        <h3 class="title">CONTRATO DE PRESTACIÓN DE SERVICIOS TÉCNICOS ESPECIALIZADOS O PROFESIONALES</h3> <br>
        <p>
            Entre los suscritos a saber: Por una parte, la sociedad <b>BULL MARKETING S.A.S.</b> legalmente constituida
            e inscrita en la Cámara de Comercio de Bogotá e identificada con NIT. 900.298.176-1 y representada
            legalmente según certificado de cámara de comercio y quien para los efectos del presente contrato se
            denominará <b>LA SOCIEDAD CONTRATANTE</b>, y por la otra el (a) Señor (a) <b>{{ $tercero->nombre }} {{ $tercero->apellido }}</b> mayor de edad, identificado como aparece al pie de su firma y quien se
            denominará <b>EL CONTRATISTA</b>, en adelante “Las Partes” manifestamos que hemos acordado en
            celebrar el presente contrato civil de <b>PRESTACIÓN DE SERVICIOS</b> que se regirá por las siguientes: <br>
        </p>
        <h3 class="title"> CLÁUSULAS: </h3>
        <p>
            <b>Primera: OBJETO.</b> - EL CONTRATISTA se obliga para con LA SOCIEDAD CONTRATANTE a prestar el
            servicio de
            <b>
                @foreach ($items as $item)
                    {{ $item->tipo_servicio }},
                @endforeach
            </b>
            para la Empresa Contratante. <b>Parágrafo Primero.</b> - No obstante, las partes en uso de su autonomía y sin que
            exista subordinación, definirán con EL CONTRATISTA los tiempos y sobre los cuales versarán los
            servicios que éste prestará de manera independiente; igualmente EL CONTRATISTA se obliga a realizar
            su actividad independiente bajo el esquema de ROLLES Y FUNCIONES, documento anexo al cual se
            obliga EL CONTRATISTA; es decir, de no cumplir las expectativas LA SOCIEDAD CONTRATANTE,
            podrá dar por finalizado el presente contrato, sin que medie indemnización alguna. <b>Segunda. - NATURALEZA JURÍDICA.</b>
            - El presente contrato es de orden mercantil, y no conlleva para EL
            CONTRATISTA relaciones de subordinación o dependencia laboral, técnica o administrativa.
            Consecuentemente, EL CONTRATISTA utilizará sus propios medios, conocimientos, técnicas y
            capacidades para prestar el servicio independiente contratado. El presente contrato tampoco genera
            relación laboral de ninguna naturaleza con Empleados o Dependientes contratados al servicio de EL
            CONTRATISTA ya que no se ampara en supuestos de subordinación jurídica ni dependencia jerárquica.
            <b>Parágrafo Primero. -</b> Es entendido que EL CONTRATISTA asume como obligación la prestación de los
            servicios dando estricto cumplimiento a todas las normas y reglamentaciones vigentes tributaria y tanto
            de carácter ético como reglamentario, contempladas en la Legislación Nacional. Igualmente asume la
            responsabilidad de carácter civil, penal o administrativa que pueda derivarse por la consumación de
            infracciones a las disposiciones vigentes, o por perjuicios ocasionados a terceros, tanto de carácter
            jurídico como económico. <b>Tercera. – CONTRAPRESTACIÓN:</b> LA SOCIEDAD CONTRATANTE
            reconocerá y pagará a EL CONTRATISTA una contraprestación a titulo de honorarios en cuantía y en los
            plazos establecidos en cada una de las ÓRDENES DE SERVICIOS semanales o quincenales, que las
            partes acuerden, y que estarán sujetas a las actividades previamente acordadas con EL CONTRATISTA,
            ordenes que haran parte anexa al presente contrato y de las cuales se determina el valor del servicio por
            cada actividad desarrollada efectivamente por EL CONTRATISTA. <b>Cuarta. – DURACIÓN:</b> El presente
            contrato tendrá la duración que fijen las partes en cada uno de los anexos ordenes de servicios o
            propuestas comerciales, según las actividades contratadas y que desarrolle efectivamente EL
            CONTRATISTA. No obstante, lo expresado, LA SOCIEDAD CONTRATANTE podrá revocar en cualquier
            tiempo, durante la ejecución del presente contrato y cuando a juicio de esta o de la asamblea de
            accionistas así lo determinen, el presente acuerdo o revocar un servicio o actividad de acuerdo con cada
            propuesta. <b>Quinta. - OBLIGACIONES DE LAS PARTES:</b> 1.- DEL CONTRATISTA. - a) A prestar sus
            servicios técnicos y/o profesionales en las condiciones y bajo los lineamientos que establezca LA
            SOCIEDAD CONTRATANTE. b) Realizar sus servicios atendiendo las directrices de LA SOCIEDAD
            CONTRATANTE, sin que este acatamiento se asemeje a subordinación laboral, pues se trata de un
            servicio especializado e independiente, con plena autonomía por parte de EL CONTRATISTA. c) Será
            - 2 -
            una obligación principal de EL CONTRATISTA, afiliarse y pagar las cotizaciones a la seguridad social
            integral como contratista independiente, y deberá allegar con cada cuenta de cobro los comprobantes
            que acrediten mensualmente o por todo el tiempo del contrato de conformidad con la cuantía de los
            servicios pactados y en los términos de Ley. 2.- DE LA SOCIEDAD CONTRATANTE: d) Disponer al
            CONTRATISTA de todos los medios necesarios para que pueda ejercer sus servicios de manera libre e
            independiente en los términos acordados y fijados en este contrato y en cada uno de los anexos
            específicos por cada servicio. e) Exigir al CONTRATISTA periódicamente el pago a la Seguridad Social
            Integral, para poder cancelar los honorarios por los servicios causados f). Pagar los honorarios
            acordados, en las fechas establecidas y durante la vigencia del contrato, según cada orden de servicios.
            <b>Sexta.- CAUSALES DE TERMINACIÓN.-</b> Acuerdan las partes que el presente contrato finalizará por una
            de las siguientes circunstancias: a) Por decisión unilateral de cualquiera de las partes: b) Por que EL
            CONTRATISTA no cumpla con sus obligaciones, en los términos aquí pactados. c) Por que EL
            CONTRATISTA no cumpla con la obligación legal de afiliación y cotización al Sistema de Seguridad
            Social Integral para los riesgos de salud, pensión y ARL a que este obligado como trabajador
            independiente, de acuerdo con las estipulaciones legales que sobre el particular exija la legislación civil,
            comercial y de Seguridad social Integral. d) Por mutuo acuerdo entre las partes. e) Por que no se generen
            servicios por más de treinta (30) días, o no se acuerde con el CONTRATISTA órdenes de servicios o no
            se disponga de los servicios del CONTRATISTA. En todos los casos, la parte que quiera dar por
            terminado el servicio aquí pactado, notificará a la otra por escrito y con una anticipación no menor de
            cinco (5) días; sin que por tal circunstancia se genere indemnización para EL CONTRATISTA. <b>Séptima:
            PROTECCIÓN DE DATOS O HABEAS DATA.-</b> EL CONTRATISTA, como consecuencia del presente
            contrato autoriza expresamente a LA SOCIEDAD CONTRATANTE a suministrar u ofrecer información
            relacionada con el presente contrato y/o con sus datos personales en los casos en que con ocasión de su
            actividad como PUBLICISTA, sean necesarios dar a conocer, por su actividad profesional o como
            consecuencia de la información suministrada ante las entidades competentes, según solicitud, expresa o
            de autoridad, exceptuando la información “sensible”, conforme al decreto 1377 del 27 de junio de 2.013,
            que reglamenta la ley 1581 del 2.012. <b>Octava:- CONFIDENCIALIDAD:</b> Las Partes reconocen y aceptan
            que toda la información relacionada con el manejo de la información es confidencial. En consecuencia, se
            obligan en estricto cumplimiento del presente contrato, a no revelar esta información durante el término
            del mismo, ni una vez terminado el Contrato por cualquier causa. Para tal efecto, las Partes se obligan a
            utilizar esta información confidencial únicamente para los fines para los cuales les fue proporcionada, la
            mantendrán en estricta reserva y no la usarán ni revelarán a ninguna persona, natural o jurídica, salvo
            que cuente con autorización expresa y escrita de la otra parte. El incumplimiento a lo establecido en esta
            cláusula por cualquiera de las Partes implica un incumplimiento de las obligaciones contractuales y dará
            derecho a la terminación del presente Contrato, sin perjuicio de las demás acciones que pueda tomar la
            parte cumplida derivadas de la violación a la confidencialidad pactada. <b>Novena:- PROHIBICIÓN DE
            CESIÓN.-</b> El presente contrato se realiza teniendo en cuenta la calidad, conocimientos técnicos y
            habilidades profesionales del CONTRATISTA y por lo tanto no puede ser cedido ni en todo ni en parte.
            <b>Décima:- LEY APLICABLE.-</b> El presente Contrato y la relación comercial que surja ente las Partes en
            virtud del mismo, se regirá por la ley de la República de Colombia y cualquier diferencia que surja entre
            las Partes como consecuencia de este Contrato deberá ser puesta primeramente en conocimiento de las
            partes, con el fin de agotar el arreglo directo, que podrá ser a través de una conciliación o transacción,
            para ello se fija un término de cinco (5) días hábiles; en caso de fracasar esta etapa; las partes acudirán a
            un Tribunal de Arbitramento, ante una entidad competentes y se seguirán las reglas allí establecidas,
            para todos los efectos legales el domicilio contractual será la ciudad de Bogotá D.C.. <b>Décima Primera.
            PERFECCIONAMIENTO.-</b> El presente contrato se perfeccionará con la firma de las partes, para
            - 3 -
            constancia se suscribe en la ciudad de Bogotá, en dos ejemplares del mismo tenor y valor, a los
            ________________ (___) días del mes de ________________ del año 20_____.
        </p>
        <table class="table">
            <tr>
                <td style="text-align: center;">
                    <p class="bold">LA SOCIEDAD CONTRATANTE. <br><br><br><br></p>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/signs/sign.png'))) }}" alt="Firma">
                    <p class="bold">__________________________</p>
                    <p class="bold">BULL MARKETING S.A.S.</p>
                    <p>Representante Legal</p>
                    <p>NIT. 900.298.176-1</p>
                </td>
                <td style="text-align: center;">
                    <p class="bold">EL CONTRATISTA. <br><br><br><br></p>
                    <p class="sign"> {{ $tercero->nombre }} {{ $tercero->apellido }}</p>
                    <p class="bold">__________________________</p>
                    <p class="bold">Nombre: {{ $tercero->nombre }} {{ $tercero->apellido }}</p>
                    <p>RUT. </p>
                    <p><br></p>
                </td>
            </tr>
        </table>
        <table class="table legal">
            <tr>
                <td>
                    <p>
                        Vo. Bo. <br>
                        Líder Legal – 01-09-2024
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
