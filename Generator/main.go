package main

import (
	"encoding/json"
	"fmt"
	"generator/utils"
	"log"
	"math/rand"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"regexp"
	"strings"
	"time"

	"github.com/otiai10/copy"
)

const templateDir string = "./template/Havenbrook/"
const exportDir string = "./export/"

func handleRequests() {
	http.HandleFunc("/", requestExport)
	log.Fatal(http.ListenAndServe(":10000", nil))
}

func requestExport(w http.ResponseWriter, r *http.Request) {
	queryParams := r.URL.Query()

	uuid := queryParams.Get("uuid")

	if len(uuid) == 0 {
		http.Error(w, "Bad Request: Missing 'uuid' parameter", http.StatusBadRequest)
		return
	}

	type JsonResponse struct {
		Passwords []string `json:"passwords"`
		Shift     int      `json:"shift"`
		File      string   `json:"file"`
	}

	passwords, shift, file := generateNew(uuid)

	response := JsonResponse{
		Passwords: passwords,
		Shift:     shift,
		File:      file,
	}

	// Encoder la structure response en JSON
	jsonResponse, err := json.Marshal(response)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}

	// Définir l'en-tête de la réponse comme JSON
	w.Header().Set("Content-Type", "application/json")

	// Écrire la réponse JSON dans la http.ResponseWriter
	w.Write(jsonResponse)
}

func main() {
	rand.NewSource(time.Now().UnixNano())

	// handleRequests()

	generateNew("lkjqskldjqlkjdq_qsd_qsdqsdàà00")
}

func generateNew(uuid string) ([]string, int, string) {
	err := utils.LoadWords()
	if err != nil {
		fmt.Errorf(err.Error())
	}

	utils.Passwords.PassList = []string{}

	newPath := exportDir + "Havenbrook-" + uuid

	err = copy.Copy(templateDir, newPath, copy.Options{
		OnSymlink: func(src string) copy.SymlinkAction {
			return copy.Shallow
		},
	})

	if err != nil {
		fmt.Println("Erreur lors de la copie du répertoire:", err)
	}

	traverseDirectory(newPath)

	return utils.Passwords.PassList, utils.Passwords.Shift, newPath
}

func traverseDirectory(path string) {
	fileInfo, err := os.Stat(path)
	if err != nil {
		fmt.Printf("Erreur lors de la lecture du fichier %s : %v\n", path, err)
		return
	}

	if fileInfo.IsDir() {
		files, err := os.ReadDir(path)
		if err != nil {
			fmt.Printf("Erreur lors de la lecture du répertoire %s : %v\n", path, err)
			return
		}

		for _, file := range files {
			traverseDirectory(filepath.Join(path, file.Name()))
		}
	} else if fileInfo.Mode().IsRegular() {
		ext := strings.ToLower(filepath.Ext(fileInfo.Name()))

		if ext == ".txt" {
			readTextFileContent(path)
		} else if ext == ".jpg" {
			exec.Command("exiftool", "-overwrite_original", `-Title="<IC>"`)
			// for _, tag := range utils.GetExifTags(path) {
			// 	replaceTag(path, tag, "")
			// }
		}
	}
}

func readTextFileContent(path string) {
	content, err := os.ReadFile(path)
	if err != nil {
		fmt.Printf("Erreur lors de la lecture du fichier %s : %v\n", path, err)
		return
	}

	tagPattern := regexp.MustCompile(`<[^>]+>`)
	tags := tagPattern.FindAllString(string(content), -1)

	if len(tags) > 0 {
		newContent := string(content) // Initialize newContent with the original content
		for _, tag := range tags {
			tag = strings.TrimFunc(tag, func(r rune) bool {
				return r == '>' || r == '<'
			})

			newContent = replaceTag(path, tag, newContent) // Accumulate modifications
		}

		err := os.WriteFile(path, []byte(newContent), 0644)

		if err != nil {
			fmt.Printf("Erreur lors de la réécriture du fichier %s : %v\n", path, err)
		}
	}
}

type ReplacementFunc func(param string) string

var tagToReplacementFunc = map[string]ReplacementFunc{
	"CC":  func(param string) string { return utils.StringToCaesar(utils.GenerateRandWord(param)) },
	"CCS": func(param string) string { return utils.StringToCaesar("Dr. Arturus") },
	"D":   func(param string) string { return utils.GenerateDate() },
	"C":   func(param string) string { return utils.GenerateRandWord(param) },
	"CB":  func(param string) string { return utils.StringToBinary(utils.GenerateRandWord(param)) },
	"CCY": func(param string) string { return utils.LatinToCyrillic(utils.GenerateRandWord(param)) },
	"CCYP": func(param string) string {
		return utils.LatinToCyrillic("Portez ce vieux whisky au juge blond qui fume.") +
			" = Portez ce vieux whisky au juge blond qui fume."
	},
}

func replaceTag(path, tag, content string) string {
	tagsParts := strings.Split(tag, ":")
	tag = tagsParts[0]
	param := ""
	if len(tagsParts) >= 2 {
		param = tagsParts[1]
	}

	if replacementFunc, exists := tagToReplacementFunc[tag]; exists {
		newContent := strings.Replace(content, "<"+tag+">", replacementFunc(param), 1)

		pathParts := strings.Split(path, "/")
		fileName := pathParts[len(pathParts)-1]

		fmt.Printf("Tag <%s> remplacé dans le fichier %s\n", tag, fileName)
		return newContent
	}

	return content
}
