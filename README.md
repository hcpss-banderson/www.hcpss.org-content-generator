# www.hcpss.org Content Generator

Generates certain pages for www.hcpss.org which rely on data from the HCPSS API.

To see a list of available commands run:

```
docker run banderson/hcpss-content-content-generator
```

To generate content for https://www.hcpss.org/schools, run:

```
docker run banderson/hcpss-content-content-generator ./bin/console content:generate:cards
```
