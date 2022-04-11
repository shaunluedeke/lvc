
# LVCharts API

A brief description of what this project does and who it's for



## API Reference

#### GET

Returned ein json string

```http
  GET https://lvcharts.de/api/index.php?id={id}&action={action}&type={type}
```

| action | type | Description                       |
| :-------- | :-------- | :-------------------------------- |
| `song` | get | du kannst &id={id} hinzufügen um 1 Song zu bekommen |
| `song` | gettop |  du kannst &id={id} hinzufügen um 1 Song zu bekommen |
| `newsong`| | du kannst &id={id} hinzufügen um 1 Song zu bekommen |
| `charts` | get | du kannst &id={id} hinzufügen um 1 Charts zu bekommen |
| `charts` | gettop | du kannst &id={id} hinzufügen um 1 Charts zu bekommen |
| `charts` | getVotesfromUser | du musst &id={id} und &userid={userid} hinzufügen um die Votes zu sehen |
