DOCKER_ACCOUNT_NAME=kawa1228
REPOSITORY_NAME=socialoutput-share
GCP_ACCOUNT_NAME=kawasumi-private

printfGreen() {
   printf "\033[32m$1\033[m\n"
}

# -----------------
# CloudRunにデプロイ
# -----------------

printfGreen "Deploy running ..."

# imageの削除
printfGreen "--- Delete docker images ---"
docker rmi -f $(docker images | grep ${REPOSITORY_NAME} | awk '{print $3}' | xargs)

# コンテナの作成
printfGreen "--- Build docker images ---"
docker build . -t ${DOCKER_ACCOUNT_NAME}/${REPOSITORY_NAME}

# gcrタグの付与
printfGreen "--- Put tag gcr.io ---"
docker tag ${DOCKER_ACCOUNT_NAME}/${REPOSITORY_NAME} gcr.io/${GCP_ACCOUNT_NAME}/${REPOSITORY_NAME}

# GCPへpush
printfGreen "--- Push docker container ---"
docker push gcr.io/${GCP_ACCOUNT_NAME}/${REPOSITORY_NAME}


printfGreen "Complete ! 👏"
