# BurbuTrading
Analizador de datos buy/ask en mercados de oferta y demanda, enfocado a mercados con cryptomonedas o tokens.

El objetivo es hacer datamining para estudiar las tendencias del valor o los comportamientos de los que cuidan del valor.

A diferencia de otros mercados, como el mercado de valores de Bolsa, el mercado de las cryptos tiene agentes que alteran con fuerza el cruce entre oferta y demanda, entre los que están:
- Los que poseen grandes cantidades de bitcoins u otras criptomonedasmonedas maduras y los invierten en tokens. su poder de arrastre de las cotizaciones es enorme y eso les obliga a dejar rastro en las frecuencias y en las distribuciones reflejados en los datos buy/ask.
- Los que poseen posiciones fuertes de una moneda en una region y en esa región hay un cambio político que los arrastra a otros productos. 
- Los que están atrapados en un exchange (un exchange es un sitio web en donde se cambian diferentes monedas, incluso dólares, euros y otros activos FIAT). Si una cryto es "endémica" de un exchange y hay una fuerte tendencia bajista o alcista, el resto de valores de ese mismo exchange tenderán a tener una diferencia notable en precio con el mismo producto de otros exchanges. Esto es debido a que hacer un carry-trade entre exchanges no es inmediato, tiene comisiones y la moneda que provoca todo esto puede estar en fuerte estado de iliquidez o incluso, de pánico.
- Los "cuidadores" de las ICO. Una ICO (Initial Coin Offer) es un proyecto empresarial en el que se reparten unos "tokens" que tienen valor en sí mismos porque dan derecho a servicios futuros o porque podrán ser comprados por otros para tener esos derechos

Existen muchas razones que nos pueden llevar a necesitar alguna herramienta de cruce de valores entre cotizadas, en el mismo exchange u otros, y hacer operaciones matemáticas inusuales o que sean engorrosas. Esta libreria y set de aplicaciones viene a facilitar la identificación de las alteraciones de mercado que puedan constituir una oportunidad de negocio.

# Lo que es BurbuTrading
- BurbuTrading es un conjunto de aplicaciones y una librería que toma datos CSV de diferentes valores y diferentes exchanges y permite datamining sobre los mismos. Un CSV (Comma Separated Value) es un archivo de texto con valores separados por comas. Los exchange suelen facilitarlos.

- Estará escrito en lenguaje PHP, con enfoque de classes (OOP, orientado al objeto).

- La salida de datos es númerica (pantalla), serial (json) y gráfica (la librería elegida es jpgraph). Otras salidas son posibles, claro, pero al menos esas tres estarán disponibles en la rama principal.

# Lo que no es BurbuTrading
- BurbuTrading no pretende tener un front-end bonito. 

- BurbuTrading no pretende ser una herramienta de trading de alta frecuencia. 

- BurbuTrading puede ser todo ésto o más, y ser tomado por otros fines, para lo cual, efectuarán un fork o un branch, si así lo estiman, pero el enfoque de la rama principal es puramente matemático y amigable a operación en terminal, sin aderezos y grandes pretensiones que al final suelen ser un lastre para el desarrollo.

# Instrucciones

En un sistema Linux con PHP instalado, instalad git:
- $ sudo apt-get install git

Elegid un directorio (carpeta) donde ponerlo y descargar el proyecto:
- $ git clone https://github.com/toledanos/burbutrading

Ejecutad el test:
- $ php main.php  

Actualmente el test consiste en una salida numérica en pantalla, a partir de un archivo descargado de Bitfinex sobre el par EOS/BTC. 
Las aplicaciones podrán usarse tambien en entornos Windows y Mac, pues GIT y PHP esta disponible en ellos. 



