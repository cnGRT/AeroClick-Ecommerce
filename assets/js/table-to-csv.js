// MIT License - https://github.com/bradtraversy/
function exportTableToCSV(filename) {
    const table = document.querySelector('.comparison-table table');
    if (!table) return;

    let csv = [];
    let rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // 清理文本，处理逗号/换行
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
            data = data.replace(/"/g, '""'); // 转义双引号
            row.push(`"${data}"`);
        }
        csv.push(row.join(","));
    }

    // 下载文件
    const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}