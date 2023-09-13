package utils

import (
	"encoding/base64"
	"fmt"
	"math/rand"
	"os"
	"os/exec"
	"regexp"
	"strconv"
	"strings"
	"time"
)

type PassDatas struct {
	PassList []string
	Shift    int
}

var Passwords PassDatas
var WordsList []string

const wordsDir string = "./utils/words.txt"

func LoadWords() error {
	content, err := os.ReadFile(wordsDir)
	if err != nil {
		return err
	}

	lines := strings.Split(string(content), "\n")

	for _, line := range lines {
		trimmedLine := strings.TrimSpace(line)
		if trimmedLine != "" {
			WordsList = append(WordsList, trimmedLine)
		}
	}

	return nil
}

func GenerateDate() string {
	min := time.Date(1970, 1, 1, 0, 0, 0, 0, time.UTC).Unix()
	max := time.Now().Unix()
	randomUnixTime := rand.Int63n(max-min) + min
	randomDate := time.Unix(randomUnixTime, 0)

	date := fmt.Sprintf("%02d-%02d-%04d", randomDate.Day(), randomDate.Month(), randomDate.Year())

	Passwords.PassList = append(Passwords.PassList, fmt.Sprintf("%d", randomDate.Day()-int(randomDate.Month())-randomDate.Year()))

	return date
}

func GenerateRandInt(small bool) int {
	if small {
		return rand.Intn(24) + 1
	} else {
		return rand.Intn(34) + 1
	}
}

func GenerateRandWord(param string) string {
	if len(param) == 0 {
		indexToRemove := rand.Intn(len(WordsList) - 1)
		param = WordsList[indexToRemove]
		WordsList = append(WordsList[:indexToRemove], WordsList[indexToRemove+1:]...)

		Passwords.PassList = append(Passwords.PassList, param)
	}

	return param
}

func GenerateBs4(word string) {
	bs64 := base64.StdEncoding.EncodeToString([]byte(word))

	for i := 0; i < rand.Intn(21); i++ {
		bs64 = base64.StdEncoding.EncodeToString([]byte(bs64))
	}
}

func StringToCaesar(word string) string {
	if Passwords.Shift == 0 {
		Passwords.Shift = GenerateRandInt(true)
	}

	result := ""
	for _, char := range word {
		if char >= 'a' && char <= 'z' {
			char = 'a' + (char-'a'+rune(Passwords.Shift))%26
		} else if char >= 'A' && char <= 'Z' {
			char = 'A' + (char-'A'+rune(Passwords.Shift))%26
		}
		result += string(char)
	}
	return result
}

func StringToBinary(word string) string {
	binaryString := ""
	for _, char := range word {
		binaryString += fmt.Sprintf("%08s", strconv.FormatInt(int64(char), 2))
	}
	return binaryString
}

func StringToBinaryArray(word string) []string {
	binaryArray := make([]string, len(word))
	for i, char := range word {
		binaryArray[i] = strconv.FormatInt(int64(char), 2)
	}
	return binaryArray
}

func LatinToCyrillic(word string) string {
	latinAlphabet := "abcdefghijklmnopqrstuvwxyz"
	cyrillicAlphabet := "абцдефгхийклмнопярстуввхуз"
	translation := map[rune]rune{}

	for i, char := range latinAlphabet {
		translation[char] = []rune(cyrillicAlphabet)[i]
	}

	cyrillicString := ""
	for _, char := range word {
		if translatedChar, ok := translation[char]; ok {
			cyrillicString += string(translatedChar)
		} else {
			cyrillicString += string(char)
		}
	}

	return cyrillicString
}

// sudo apt-get install libimage-exiftool-perl
func SetExifTag(word, imagePath string) {
	cmd := exec.Command("exiftool", "-overwrite_original", "-Title=Le code est : "+word, imagePath)
	output, err := cmd.CombinedOutput()

	if err != nil {
		fmt.Printf("Error running exiftool: %v\n%s", err, output)
	}
}

func GetExifTags(imagePath string) []string {
	cmd := exec.Command("exiftool", "-Title", imagePath)
	output, err := cmd.CombinedOutput()

	if err != nil {
		fmt.Printf("Error running exiftool: %v\n%s", err, output)
	}

	tagPattern := regexp.MustCompile(`<[^>]+>`)
	return tagPattern.FindAllString(string(output), -1)
}
